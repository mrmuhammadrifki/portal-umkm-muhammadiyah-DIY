<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subsector;
use App\Models\UmkmProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilan utama Kelola Kategori & 21 Subsektor EKRAF
     */
    public function index(Request $request): View
    {
        // 1. Data 3 Kategori Skala Usaha (Mikro, Kecil, Menengah)
        $categories = Category::all();

        // Hitung statistik UMKM approved per kategori skala pendapatan
        $approvedQuery = UmkmProfile::where('status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_active', true));

        $totalApproved = (clone $approvedQuery)->count();

        $scaleStats = [
            'mikro' => (clone $approvedQuery)->where(function ($q) {
                $q->where('monthly_revenue', '<', 25_000_000)
                  ->orWhere('category_id', 1);
            })->count(),

            'kecil' => (clone $approvedQuery)->where(function ($q) {
                $q->whereBetween('monthly_revenue', [25_000_000, 208_000_000])
                  ->orWhere('category_id', 2);
            })->count(),

            'menengah' => (clone $approvedQuery)->where(function ($q) {
                $q->where('monthly_revenue', '>', 208_000_000)
                  ->orWhere('category_id', 3);
            })->count(),

            'belum_isi' => (clone $approvedQuery)->whereNull('monthly_revenue')->count(),
            'total'     => $totalApproved,
        ];

        // 2. Data 21 Subsektor Ekonomi Kreatif (EKRAF)
        $subsectorQuery = Subsector::withCount([
            'products',
            'umkmProfiles' => fn ($q) => $q->where('status', 'approved'),
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $subsectorQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $subsectors = $subsectorQuery->orderBy('id')->get();

        $statsEkraf = [
            'total_subsektor' => Subsector::count(),
            'total_produk'    => \App\Models\Product::count(),
            'subsektor_aktif' => Subsector::has('products')->orHas('umkmProfiles')->count(),
        ];

        return view('admin.kategori.index', [
            'categories' => $categories,
            'scaleStats' => $scaleStats,
            'subsectors' => $subsectors,
            'statsEkraf' => $statsEkraf,
        ]);
    }

    public function create(): View
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
            'min_revenue' => ['nullable', 'integer', 'min:0'],
            'max_revenue' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Category::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'min_revenue' => $validated['min_revenue'] ?? 0,
            'max_revenue' => $validated['max_revenue'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori skala usaha berhasil ditambahkan.');
    }

    public function edit(Category $kategori): View
    {
        return view('admin.kategori.edit', ['category' => $kategori]);
    }

    public function update(Request $request, Category $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name,' . $kategori->id],
            'min_revenue' => ['nullable', 'integer', 'min:0'],
            'max_revenue' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $kategori->update([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'min_revenue' => $validated['min_revenue'] ?? 0,
            'max_revenue' => $validated['max_revenue'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', "Kategori '{$kategori->name}' berhasil diperbarui.");
    }

    public function destroy(Category $kategori): RedirectResponse
    {
        if (in_array(strtolower($kategori->slug), ['mikro', 'kecil', 'menengah'])) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori inti (Mikro, Kecil, Menengah) tidak boleh dihapus karena merupakan klasifikasi acuan regulasi.');
        }

        if ($kategori->products()->exists() || $kategori->umkmProfiles()->exists()) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh data UMKM atau produk.');
        }

        $name = $kategori->name;
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
