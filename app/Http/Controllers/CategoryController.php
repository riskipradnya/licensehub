<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        // Mengambil seluruh kategori beserta jumlah lisensi aktif (atau semua) yang terhubung
        $categories = Category::withCount('licenses')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        // Secara default, karena ada kolom slug (berdasarkan pengecekan model), kita buat slug dari name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('licenses.categories.index')
                         ->with('success', "Kategori {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('licenses.categories.index')
                         ->with('success', "Kategori {$validated['name']} berhasil diperbarui.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Safe delete mechanism: Cegah penghapusan jika ada lisensi yang menggunakan kategori ini
        if ($category->licenses()->exists()) {
            return redirect()->route('licenses.categories.index')
                             ->with('error', "Gagal menghapus kategori '{$category->name}' karena masih terhubung dengan lisensi aktif.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('licenses.categories.index')
                         ->with('success', "Kategori {$categoryName} berhasil dihapus.");
    }
}
