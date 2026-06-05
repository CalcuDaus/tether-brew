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
}
