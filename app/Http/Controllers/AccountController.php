<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();
        
        // Scope accounts if the user is Admin (Riders can't access this anyway)
        if (auth()->user()->isAdmin()) {
            $query->forBranch(activeBranchId());
        }

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $accounts = $query->paginate(10)->withQueryString();
        $branches = \App\Models\Branch::active()->get();
        return view('accounts.index', compact('accounts', 'branches'));
    }

    public function create()
    {
        $branches = \App\Models\Branch::active()->get();
        return view('accounts.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'whatsapp' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['owner', 'admin', 'rider'])],
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'role' => $validated['role'],
            'branch_id' => $validated['role'] === 'owner' ? null : ($validated['branch_id'] ?? null),
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    public function edit(User $account)
    {
        $branches = \App\Models\Branch::active()->get();
        return view('accounts.edit', compact('account', 'branches'));
    }

    public function update(Request $request, User $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($account->id)],
            'whatsapp' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['owner', 'admin', 'rider'])],
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Prevent owner from changing their own role to non-owner if they are the only owner,
        // or just rely on the fact that if they change their own role, they may lose access.
        // For safety, let's prevent changing own role if logged in.
        if (auth()->id() === $account->id && $validated['role'] !== 'owner') {
            return back()->withErrors(['role' => 'Anda tidak bisa mengubah peran Anda sendiri dari owner.'])->withInput();
        }

        $account->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'role' => $validated['role'],
            'branch_id' => $validated['role'] === 'owner' ? null : ($validated['branch_id'] ?? null),
        ]);

        if (!empty($validated['password'])) {
            $account->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui!');
    }

    public function destroy(User $account)
    {
        // Prevent deleting own account
        if (auth()->id() === $account->id) {
            return redirect()->route('accounts.index')->withErrors(['hapus' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        // If rider, detach from carts
        if ($account->isRider()) {
            $account->carts()->update(['user_id' => null]);
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus!');
    }
}
