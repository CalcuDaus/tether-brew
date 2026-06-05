<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class BarController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'bar')->forBranch(activeBranchId())->latest();

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $bars = $query->paginate(10)->withQueryString();

        return view('bars.index', compact('bars'));
    }

    public function create()
    {
        return view('bars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'bar',
            'branch_id' => activeBranchId(),
        ]);

        return redirect()->route('bars.index')->with('success', 'Akun Bar berhasil ditambahkan!');
    }

    public function edit(User $bar)
    {
        abort_if($bar->role !== 'bar', 404);
        
        return view('bars.edit', compact('bar'));
    }

    public function update(Request $request, User $bar)
    {
        abort_if($bar->role !== 'bar', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($bar->id)],
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $bar->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $bar->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('bars.index')->with('success', 'Akun Bar berhasil diperbarui!');
    }

    public function destroy(User $bar)
    {
        abort_if($bar->role !== 'bar', 404);
        
        // Cannot delete self if somehow bar tries to delete themselves, but usually bar won't access this.
        if (auth()->id() === $bar->id) {
            return redirect()->route('bars.index')->withErrors(['hapus' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $bar->delete();

        return redirect()->route('bars.index')->with('success', 'Akun Bar berhasil dihapus!');
    }
}
