<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Journal;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Journal::forBranch(activeBranchId())->with(['creator', 'category']);

        if ($request->filled('category_id')) {
            $query->where('journal_category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $categories = \App\Models\JournalCategory::orderBy('name', 'asc')->get();
        
        $totalDebit = (clone $query)->where('type', 'debit')->sum('amount');
        $totalCredit = (clone $query)->where('type', 'credit')->sum('amount');
        $balance = $totalDebit - $totalCredit;

        if ($request->has('print')) {
            $journals = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        } else {
            $journals = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        }

        return view('admin.journals.index', compact('journals', 'totalDebit', 'totalCredit', 'balance', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\JournalCategory::orderBy('name', 'asc')->get();
        return view('admin.journals.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'journal_category_id' => 'nullable|exists:journal_categories,id',
        ]);

        Journal::create([
            'date' => $request->date,
            'description' => $request->description,
            'type' => $request->type,
            'amount' => $request->amount,
            'journal_category_id' => $request->journal_category_id,
            'created_by' => auth()->id(),
            'branch_id' => activeBranchId(),
        ]);

        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil ditambahkan.');
    }
    
    public function destroy(Journal $journal)
    {
        $journal->delete();
        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil dihapus.');
    }

    public function destroyAll(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isOwner()) {
            abort(403, 'Unauthorized action.');
        }

        Journal::forBranch(activeBranchId())->delete();

        return redirect()->route('admin.journals.index')->with('success', 'Seluruh data jurnal pada cabang ini berhasil dihapus.');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return redirect()->route('admin.journals.index')->with('error', 'Format file tidak didukung. Gunakan file .xlsx, .xls, atau .csv');
        }

        try {
            $filePath = $request->file('file')->getRealPath();
            $file = fopen($filePath, 'r');
            if (!$file) {
                return redirect()->route('admin.journals.index')->with('error', 'Gagal membaca file.');
            }

            // Auto-detect delimiter: read first line to check if semicolon or comma
            $firstLine = fgets($file);
            rewind($file); // Reset pointer to beginning

            $delimiter = ',';
            if ($firstLine) {
                $semicolonCount = substr_count($firstLine, ';');
                $commaCount = substr_count($firstLine, ',');
                if ($semicolonCount > $commaCount) {
                    $delimiter = ';';
                }
            }

            // Read header row with detected delimiter
            $header = fgetcsv($file, 0, $delimiter);
            if (!$header) {
                fclose($file);
                return redirect()->route('admin.journals.index')->with('error', 'File kosong atau format header tidak valid.');
            }

            // Normalize headers (lowercase, trim, remove BOM)
            $header = array_map(function ($h) {
                $h = preg_replace('/\x{FEFF}/u', '', $h); // Remove UTF-8 BOM
                return strtolower(trim($h));
            }, $header);

            // Load categories for matching
            $categories = \App\Models\JournalCategory::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
                return [strtolower(trim($key)) => $item];
            });

            // Fallback "Lain-lain" category
            $lainLainCat = \App\Models\JournalCategory::where('name', 'like', '%lain%')->first();
            if (!$lainLainCat) {
                $lainLainCat = \App\Models\JournalCategory::create(['name' => 'Lain-lain']);
            }
            $lainLainCategoryId = $lainLainCat->id;

            $imported = 0;
            $skipped = 0;

            while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                // Map row to header keys
                $data = [];
                foreach ($header as $i => $key) {
                    $data[$key] = $row[$i] ?? null;
                }

                // Skip empty rows
                if (empty($data['tanggal']) && empty($data['keterangan'])) {
                    $skipped++;
                    continue;
                }

                // Parse Date
                $date = now()->format('Y-m-d');
                if (!empty($data['tanggal'])) {
                    try {
                        $date = \Carbon\Carbon::parse($data['tanggal'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $date = now()->format('Y-m-d');
                    }
                }

                // Parse Debit/Kredit (handle Indonesian number format: 2.250.000 -> 2250000)
                $debit = $this->parseIndonesianNumber($data['debit'] ?? '');
                $kredit = $this->parseIndonesianNumber($data['kredit'] ?? '');

                if ($debit > 0) {
                    $type = 'debit';
                    $amount = $debit;
                } elseif ($kredit > 0) {
                    $type = 'credit';
                    $amount = $kredit;
                } else {
                    $skipped++;
                    continue;
                }

                // Category matching (Referensi)
                $categoryId = $lainLainCategoryId;
                if (!empty($data['referensi'])) {
                    $refName = strtolower(trim($data['referensi']));
                    if (isset($categories[$refName])) {
                        $categoryId = $categories[$refName];
                    }
                }

                $description = trim($data['keterangan'] ?? '-');
                if (empty($description)) $description = '-';

                Journal::create([
                    'date' => $date,
                    'description' => $description,
                    'type' => $type,
                    'amount' => $amount,
                    'journal_category_id' => $categoryId,
                    'branch_id' => activeBranchId(),
                    'created_by' => auth()->id(),
                ]);
                $imported++;
            }

            fclose($file);

            $msg = "Berhasil mengimport {$imported} data jurnal.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati karena data kosong)";
            }
            return redirect()->route('admin.journals.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.journals.index')->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Parse Indonesian-format number (e.g. "2.250.000" or " 2.250.000 ") into integer.
     * Dots are used as thousand separators, not decimals.
     */
    private function parseIndonesianNumber($value): float
    {
        if (empty($value)) return 0;
        $value = trim($value);
        // Remove dots (thousand separators) and spaces
        $value = str_replace(['.', ' '], '', $value);
        // Remove any remaining non-numeric chars except minus
        $value = preg_replace('/[^0-9\-]/', '', $value);
        return (float) $value;
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_jurnal.csv"',
        ];

        $columns = ['Tanggal', 'No Dokumen', 'Referensi', 'Debit', 'Kredit', 'Keterangan'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, [now()->format('Y-m-d'), 'CASH', 'Lain-lain', '100000', '', 'Pembelian ATK']);
            fputcsv($file, [now()->format('Y-m-d'), 'CASH', 'Lain-lain', '', '50000', 'Bayar Listrik']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
