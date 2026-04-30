<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    // ==========================================
    // PUBLIC METHODS
    // ==========================================

    /**
     * Halaman daftar semua artikel (publik).
     */
    public function index()
    {
        $artikels = Artikel::published()
            ->latest('published_at')
            ->get();

        return view('artikel.index', compact('artikels'));
    }

    /**
     * Halaman detail artikel berdasarkan slug (publik).
     */
    public function show(string $slug)
    {
        $artikel = Artikel::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('artikel.show', compact('artikel'));
    }

    // ==========================================
    // ADMIN CRUD METHODS
    // ==========================================

    /**
     * Admin: list semua artikel (tabel + pagination + search).
     */
    public function adminIndex(Request $request)
    {
        $query = Artikel::with('user')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $artikels = $query->paginate(10)->withQueryString();

        return view('artikel.admin.index', compact('artikels'));
    }

    /**
     * Admin: form tambah artikel.
     */
    public function create()
    {
        return view('artikel.admin.create');
    }

    /**
     * Admin: simpan artikel baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'category'     => 'required|string|max:100',
            'cover_image'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'boolean',
            'read_time'    => 'nullable|integer|min:1|max:60',
        ]);

        $validated['slug'] = Artikel::generateSlug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['user_id'] = auth()->id();
        $validated['read_time'] = $validated['read_time'] ?? $this->estimateReadTime($validated['content']);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('artikel', 'public');
        }

        Artikel::create($validated);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil ditambahkan!');
    }

    /**
     * Admin: form edit artikel.
     */
    public function edit(Artikel $artikel)
    {
        return view('artikel.admin.edit', compact('artikel'));
    }

    /**
     * Admin: update artikel.
     */
    public function update(Request $request, Artikel $artikel)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'category'     => 'required|string|max:100',
            'cover_image'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'boolean',
            'read_time'    => 'nullable|integer|min:1|max:60',
        ]);

        // Regenerate slug only if title changed
        if ($artikel->title !== $validated['title']) {
            $validated['slug'] = Artikel::generateSlug($validated['title']);
        }

        $validated['is_published'] = $request->has('is_published');

        // Set published_at only if newly published
        if ($validated['is_published'] && !$artikel->published_at) {
            $validated['published_at'] = now();
        } elseif (!$validated['is_published']) {
            $validated['published_at'] = null;
        }

        $validated['read_time'] = $validated['read_time'] ?? $this->estimateReadTime($validated['content']);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($artikel->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($artikel->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('artikel', 'public');
        }

        $artikel->update($validated);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Admin: hapus artikel.
     */
    public function destroy(Artikel $artikel)
    {
        // Delete cover image if exists
        if ($artikel->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($artikel->cover_image);
        }

        $artikel->delete();

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus!');
    }

    /**
     * Estimasi waktu baca berdasarkan jumlah kata.
     */
    private function estimateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = max(1, (int) ceil($wordCount / 200));
        return $minutes;
    }
}
