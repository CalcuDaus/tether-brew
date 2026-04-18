<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = User::latest()->paginate(10);
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'whatsapp' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['owner', 'admin', 'rider'])],
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    public function edit(User $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, User $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($account->id)],
            'whatsapp' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['owner', 'admin', 'rider'])],
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
