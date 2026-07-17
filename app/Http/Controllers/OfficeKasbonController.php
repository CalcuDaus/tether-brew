<?php

namespace App\Http\Controllers;

use App\Models\OfficeKasbon;
use App\Models\OfficeKasbonPayment;
use App\Models\Journal;
use App\Models\JournalCategory;
use Illuminate\Http\Request;

class OfficeKasbonController extends Controller
{
    public function index(Request $request)
    {
        $query = OfficeKasbon::forBranch(activeBranchId())
            ->with(['admin', 'payments'])
            ->orderBy('date', 'desc');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $totalKasbon = (clone $query)->sum('amount');
        $totalTerbayar = (clone $query)->sum('paid_amount');
        $totalSisa = $totalKasbon - $totalTerbayar;

        $finances = $query->paginate(10)->withQueryString();

        return view('admin.office_kasbon.index', compact('finances', 'totalKasbon', 'totalTerbayar', 'totalSisa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ], [
            'name.required' => 'Nama penerima kasbon harus diisi',
            'date.required' => 'Tanggal harus diisi',
            'amount.required' => 'Jumlah harus diisi',
            'amount.min' => 'Jumlah minimal Rp 1'
        ]);

        $kasbon = OfficeKasbon::create([
            'branch_id' => activeBranchId(),
            'admin_id' => auth()->id(),
            'name' => $request->name,
            'date' => $request->date,
            'amount' => $request->amount,
            'status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        // Jurnal Otomatis: Kredit (Kas Keluar)
        $category = JournalCategory::firstOrCreate(['name' => 'Kasbon Office']);
        Journal::create([
            'branch_id' => activeBranchId(),
            'created_by' => auth()->id(),
            'journal_category_id' => $category->id,
            'date' => $request->date,
            'type' => 'credit',
            'amount' => $request->amount,
            'description' => "Kasbon Office: {$request->name}" . ($request->notes ? " - {$request->notes}" : ''),
        ]);

        return redirect()->back()->with('success', 'Data Kasbon Office berhasil disimpan dan dicatat ke Jurnal.');
    }

    public function destroy(OfficeKasbon $officeKasbon)
    {
        // Cancel journals before deleting
        $category = JournalCategory::firstOrCreate(['name' => 'Kasbon Office']);
        $descriptionPrefix = "Kasbon Office: {$officeKasbon->name}";
        
        // Hapus jurnal terkait (bisa mengandalkan query karena gak ada foreign key ke kasbon di jurnal)
        Journal::where('branch_id', $officeKasbon->branch_id)
            ->where('journal_category_id', $category->id)
            ->where('type', 'credit')
            ->where('amount', $officeKasbon->amount)
            ->where('description', 'like', $descriptionPrefix . '%')
            ->whereDate('date', $officeKasbon->date)
            ->delete();

        $officeKasbon->delete();

        return redirect()->back()->with('success', 'Data Kasbon Office berhasil dihapus.');
    }

    public function storePayment(Request $request, OfficeKasbon $officeKasbon)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ], [
            'date.required' => 'Tanggal harus diisi',
            'amount.required' => 'Jumlah harus diisi',
            'amount.min' => 'Jumlah minimal Rp 1'
        ]);

        $sisa = $officeKasbon->amount - $officeKasbon->paid_amount;
        if ($request->amount > $sisa) {
            return redirect()->back()->with('error', 'Jumlah pembayaran melebihi sisa kasbon (Sisa: Rp ' . number_format($sisa, 0, ',', '.') . ').');
        }

        OfficeKasbonPayment::create([
            'office_kasbon_id' => $officeKasbon->id,
            'admin_id' => auth()->id(),
            'date' => $request->date,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        $officeKasbon->paid_amount += $request->amount;
        if ($officeKasbon->paid_amount >= $officeKasbon->amount) {
            $officeKasbon->status = 'paid';
        } else {
            $officeKasbon->status = 'partial';
        }
        $officeKasbon->save();

        // Jurnal Otomatis: Debit (Kas Masuk - pembayaran kasbon)
        $category = JournalCategory::firstOrCreate(['name' => 'Pembayaran Kasbon Office']);
        Journal::create([
            'branch_id' => activeBranchId(),
            'created_by' => auth()->id(),
            'journal_category_id' => $category->id,
            'date' => $request->date,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => "Pembayaran Kasbon Office: {$officeKasbon->name}" . ($request->notes ? " - {$request->notes}" : ''),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil disimpan dan dicatat ke Jurnal.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return redirect()->route('admin.office_kasbon.index')->with('error', 'Format file tidak didukung. Gunakan file .xlsx, .xls, atau .csv');
        }

        try {
            $filePath = $request->file('file')->getRealPath();
            $file = fopen($filePath, 'r');
            if (!$file) {
                return redirect()->route('admin.office_kasbon.index')->with('error', 'Gagal membaca file.');
            }

            // Auto-detect delimiter
            $firstLine = fgets($file);
            rewind($file);

            $delimiter = ',';
            if ($firstLine) {
                $semicolonCount = substr_count($firstLine, ';');
                $commaCount = substr_count($firstLine, ',');
                if ($semicolonCount > $commaCount) {
                    $delimiter = ';';
                }
            }

            $header = fgetcsv($file, 0, $delimiter);
            if (!$header) {
                fclose($file);
                return redirect()->route('admin.office_kasbon.index')->with('error', 'File kosong atau format header tidak valid.');
            }

            // Normalize headers
            $header = array_map(function ($h) {
                $h = preg_replace('/\x{FEFF}/u', '', $h); 
                return strtolower(trim($h));
            }, $header);

            $imported = 0;
            $skipped = 0;
            $lastYear = date('Y');

            $category = JournalCategory::firstOrCreate(['name' => 'Kasbon Office (Keluaran)']);

            while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                $data = [];
                foreach ($header as $i => $key) {
                    $data[$key] = $row[$i] ?? null;
                }

                // Check for minimal required fields
                if (empty($data['nama']) && empty($data['jumlah kasbon'])) {
                    $skipped++;
                    continue;
                }

                $name = trim($data['nama'] ?? '-');
                if (empty($name)) $name = '-';

                $dateStr = trim($data['tgl'] ?? '');
                $date = now()->format('Y-m-d');
                if (!empty($dateStr)) {
                    // Check if it's missing year (e.g. 13-Jun)
                    if (strlen($dateStr) <= 6 && preg_match('/^\d{1,2}-[a-zA-Z]{3}$/', $dateStr)) {
                        $dateStr .= '-' . $lastYear;
                    }
                    try {
                        $parsedDate = \Carbon\Carbon::parse($dateStr);
                        $date = $parsedDate->format('Y-m-d');
                        $lastYear = $parsedDate->format('Y');
                    } catch (\Exception $e) {
                        // fallback to today if parsing fails
                        $date = now()->format('Y-m-d');
                    }
                }

                $amount = $this->parseIndonesianNumber($data['jumlah kasbon'] ?? '');
                
                if ($amount <= 0) {
                    $skipped++;
                    continue;
                }

                OfficeKasbon::create([
                    'branch_id' => activeBranchId(),
                    'admin_id' => auth()->id(),
                    'name' => $name,
                    'date' => $date,
                    'amount' => $amount,
                    'status' => 'unpaid',
                    'notes' => 'Import Excel'
                ]);

                // Create Journal Entry
                Journal::create([
                    'branch_id' => activeBranchId(),
                    'created_by' => auth()->id(),
                    'journal_category_id' => $category->id,
                    'date' => $date,
                    'type' => 'credit',
                    'amount' => $amount,
                    'description' => "Kasbon Office: {$name}",
                ]);

                $imported++;
            }

            fclose($file);

            $msg = "Berhasil mengimport {$imported} data kasbon office.";
            if ($skipped > 0) {
                $msg .= " ({$skipped} baris dilewati karena data tidak valid/kosong)";
            }
            return redirect()->route('admin.office_kasbon.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('admin.office_kasbon.index')->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    private function parseIndonesianNumber($value): float
    {
        if (empty($value)) return 0;
        $value = trim($value);
        $value = str_replace(['.', ' '], '', $value);
        $value = preg_replace('/[^0-9\-]/', '', $value);
        return (float) $value;
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_kasbon_office.csv"',
        ];

        $columns = ['NO', 'NAMA', 'TGL', 'JUMLAH KASBON'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['1', 'ANWAR', '03-Jun-26', '40.000']);
            fputcsv($file, ['2', 'JHON', '13-Jun', '1.000.000']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
