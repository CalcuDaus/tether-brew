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

        $journals = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

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
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\JournalsImport, $request->file('file'));
            return redirect()->route('admin.journals.index')->with('success', 'Data jurnal berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.journals.index')->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
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
