<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalCategory;

class JournalCategoryController extends Controller
{
    public function index()
    {
        $categories = JournalCategory::orderBy('name', 'asc')->get();
        return view('admin.journal_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:journal_categories,name',
        ]);

        JournalCategory::create(['name' => $request->name]);

        return redirect()->route('admin.journal_categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, JournalCategory $journalCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:journal_categories,name,' . $journalCategory->id,
        ]);

        $journalCategory->update(['name' => $request->name]);

        return redirect()->route('admin.journal_categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(JournalCategory $journalCategory)
    {
        // Check if category is used in any journals
        if ($journalCategory->journals()->count() > 0) {
            return redirect()->route('admin.journal_categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam jurnal.');
        }

        $journalCategory->delete();

        return redirect()->route('admin.journal_categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
