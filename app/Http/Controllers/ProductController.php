<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * F14: Admin listing + search semua produk lintas UMKM untuk moderasi.
     */
    public function adminIndex(Request $request): View
    {
        $this->authorize('moderate', \App\Models\UmkmProfile::class);

        $query = Product::query()->with(['umkmProfile', 'category', 'images']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.produk.index', ['products' => $products, 'categories' => $categories]);
    }

    private function ownProfileOrAbort(Request $request)
    {
        $profile = $request->user()->umkmProfile;

        abort_if(! $profile, 403, 'Lengkapi profil usaha terlebih dahulu sebelum menambah produk.');

        return $profile;
    }

    public function index(Request $request): View
    {
        $profile = $this->ownProfileOrAbort($request);

        $products = $profile->products()->with(['category', 'images'])->latest()->get();

        return view('produk.index', ['products' => $products]);
    }

    public function create(Request $request): View
    {
        $this->ownProfileOrAbort($request);

        $categories = Category::orderBy('name')->get();

        return view('produk.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $this->ownProfileOrAbort($request);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $product = $profile->products()->create($validated);

        foreach ($images as $index => $file) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $file->store('product-images', 'public'),
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Request $request, Product $produk): View
    {
        $this->authorize('update', $produk);

        $categories = Category::orderBy('name')->get();

        return view('produk.edit', ['product' => $produk, 'categories' => $categories]);
    }

    public function update(Request $request, Product $produk): RedirectResponse
    {
        $this->authorize('update', $produk);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $produk->update($validated);

        if ($images) {
            $nextOrder = (int) $produk->images()->max('sort_order') + 1;
            foreach ($images as $index => $file) {
                ProductImage::create([
                    'product_id' => $produk->id,
                    'image_path' => $file->store('product-images', 'public'),
                    'sort_order' => $nextOrder + $index,
                ]);
            }
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus satu foto dari galeri produk (bukan hapus produk).
     */
    public function destroyImage(Request $request, Product $produk, ProductImage $image): RedirectResponse
    {
        $this->authorize('update', $produk);

        abort_unless($image->product_id === $produk->id, 404);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->route('produk.edit', $produk)->with('success', 'Foto produk dihapus.');
    }

    public function destroy(Request $request, Product $produk): RedirectResponse
    {
        $this->authorize('delete', $produk);

        if ($produk->image_path) {
            Storage::disk('public')->delete($produk->image_path);
        }
        foreach ($produk->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
