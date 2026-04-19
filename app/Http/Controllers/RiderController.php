<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RiderController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'rider')->with('carts')->latest();

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        $riders = $query->paginate(10)->withQueryString();

        return view('riders.index', compact('riders'));
    }

    public function create()
    {
        return view('riders.create');
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
            'role' => 'rider',
        ]);

        return redirect()->route('riders.index')->with('success', 'Rider berhasil ditambahkan!');
    }

    public function edit(User $rider)
    {
        abort_if($rider->role !== 'rider', 404);
        $rider->load('carts');

        // Get carts that are unassigned OR already assigned to this rider
        $availableCarts = Cart::where('user_id', null)
            ->orWhere('user_id', $rider->id)
            ->get();

        return view('riders.edit', compact('rider', 'availableCarts'));
    }

    public function update(Request $request, User $rider)
    {
        abort_if($rider->role !== 'rider', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($rider->id)],
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'cart_ids' => 'nullable|array',
            'cart_ids.*' => 'exists:carts,id',
        ]);

        $rider->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $rider->update(['password' => Hash::make($validated['password'])]);
        }

        // Unassign all carts currently assigned to this rider
        Cart::where('user_id', $rider->id)->update(['user_id' => null]);

        // Assign selected carts to this rider
        if (!empty($validated['cart_ids'])) {
            Cart::whereIn('id', $validated['cart_ids'])
                ->where(function ($q) use ($rider) {
                    $q->whereNull('user_id')->orWhere('user_id', $rider->id);
                })
                ->update(['user_id' => $rider->id]);
        }

        return redirect()->route('riders.index')->with('success', 'Rider berhasil diperbarui!');
    }

    public function destroy(User $rider)
    {
        abort_if($rider->role !== 'rider', 404);

        // Unassign rider from all carts first
        $rider->carts()->update(['user_id' => null]);

        $rider->delete();

        return redirect()->route('riders.index')->with('success', 'Rider berhasil dihapus!');
    }
}
