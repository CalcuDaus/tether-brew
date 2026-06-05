<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan!');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('branches')->ignore($branch->id)],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui!');
    }

    public function destroy(Branch $branch)
    {
        // Don't allow deleting if it has related data
        if ($branch->users()->count() > 0 || $branch->carts()->count() > 0) {
            return redirect()->route('branches.index')->with('error', 'Cabang tidak bisa dihapus karena masih memiliki data terkait (User/Cart). Nonaktifkan saja cabang ini.');
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus!');
    }

    // Switch active branch
    public function switchBranch(Request $request, Branch $branch)
    {
        // Ensure user is owner
        if (!$request->user()->isOwner()) {
            abort(403);
        }

        if (!$branch->is_active) {
            return back()->with('error', 'Tidak bisa pindah ke cabang yang tidak aktif.');
        }

        session(['active_branch_id' => $branch->id]);

        return back()->with('success', "Berhasil pindah ke {$branch->name}.");
    }
}
