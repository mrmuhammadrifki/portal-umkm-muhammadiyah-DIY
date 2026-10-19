<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\UmkmProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UmkmController extends Controller
{
    /**
     * F8, F10: Katalog publik UMKM — listing approved, search nama usaha,
     * filter wilayah (kabupaten/kota) dan kategori produk yang dijual.
     */
    public function halamanUtama(Request $request): View
    {
        $query = UmkmProfile::query()
            ->where('status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_active', true));

        if ($request->filled('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('wilayah')) {
            $query->where('kabupaten_kota', $request->wilayah);
        }

        if ($request->filled('kategori')) {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('category_id', $request->kategori)->where('status', 'active');
            });
        }

        $umkms = $query->latest()->paginate(9)->withQueryString();

        $categories = Category::withCount(['products' => function ($q) {
            $q->where('status', 'active')->whereHas('umkmProfile', fn ($q2) => $q2->where('status', 'approved'));
        }])->orderBy('name')->get();

        $wilayahList = UmkmProfile::where('status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->whereNotNull('kabupaten_kota')
            ->distinct()
            ->orderBy('kabupaten_kota')
            ->pluck('kabupaten_kota');

        $stats = [
            'umkm' => UmkmProfile::where('status', 'approved')->whereHas('user', fn ($q) => $q->where('is_active', true))->count(),
            'produk' => Product::where('status', 'active')->whereHas('umkmProfile', fn ($q) => $q->where('status', 'approved'))->count(),
            'wilayah' => $wilayahList->count(),
        ];

        return view('welcome', [
            'umkms' => $umkms,
            'categories' => $categories,
            'wilayahList' => $wilayahList,
            'stats' => $stats,
        ]);
    }

    /**
     * F11: Halaman detail UMKM — profil + daftar produk aktif + kontak WA.
     */
    public function detail(Request $request, $id): View
    {
        $umkm = UmkmProfile::where('status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->findOrFail($id);

        $productQuery = $umkm->products()->where('status', 'active')->with(['category', 'images']);

        if ($request->filled('search')) {
            $productQuery->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $productQuery->where('category_id', $request->kategori);
        }

        $products = $productQuery->latest()->get();

        // Kategori yang dipakai produk UMKM ini saja (bukan semua kategori sistem)
        // supaya dropdown filter tidak menampilkan kategori yang pasti kosong.
        $categories = Category::whereHas('products', fn ($q) => $q->where('umkm_id', $umkm->id)->where('status', 'active'))
            ->orderBy('name')
            ->get();

        return view('detail-umkm', ['umkm' => $umkm, 'products' => $products, 'categories' => $categories]);
    }

    /**
     * F9, F10: Katalog publik produk — listing active dari UMKM approved,
     * search nama produk, filter kategori.
     */
    public function produkPublik(Request $request): View
    {
        $query = Product::query()
            ->where('status', 'active')
            ->whereHas('umkmProfile', fn ($q) => $q->where('status', 'approved')
                ->whereHas('user', fn ($q2) => $q2->where('is_active', true)))
            ->with(['umkmProfile', 'category', 'images']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('produk-publik', ['products' => $products, 'categories' => $categories]);
    }

    /**
     * F12: Halaman detail produk publik.
     */
    public function produkDetail(Product $product): View
    {
        $product->load(['umkmProfile.user', 'category', 'images']);

        abort_unless(
            $product->status === 'active'
                && $product->umkmProfile->status === 'approved'
                && $product->umkmProfile->user->is_active,
            404
        );

        return view('produk-detail', ['product' => $product]);
    }

    /**
     * Pesan ke UMKM via WhatsApp — hanya untuk pengguna yang sudah login.
     * Nomor tidak dirender di halaman publik; guest diarahkan login dulu
     * (middleware auth) lalu kembali ke sini dan diteruskan ke wa.me.
     */
    public function hubungiUmkm(Request $request, $id): RedirectResponse
    {
        $umkm = UmkmProfile::where('status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->findOrFail($id);

        $request->user()->whatsappOrders()->create([
            'umkm_id'       => $umkm->id,
            'business_name' => $umkm->business_name,
        ]);

        return $this->whatsappRedirect(
            $umkm,
            "Halo {$umkm->business_name}, saya {$request->user()->name} ingin memesan produk Anda yang saya lihat di Portal UMKM Muhammadiyah DIY."
        );
    }

    public function hubungiProduk(Request $request, Product $product): RedirectResponse
    {
        $product->load('umkmProfile.user');

        abort_unless(
            $product->status === 'active'
                && $product->umkmProfile->status === 'approved'
                && $product->umkmProfile->user->is_active,
            404
        );

        $request->user()->whatsappOrders()->create([
            'umkm_id'       => $product->umkm_id,
            'product_id'    => $product->id,
            'business_name' => $product->umkmProfile->business_name,
            'product_name'  => $product->name,
        ]);

        return $this->whatsappRedirect(
            $product->umkmProfile,
            "Halo {$product->umkmProfile->business_name}, saya {$request->user()->name} ingin memesan produk {$product->name} yang saya lihat di Portal UMKM."
        );
    }

    private function whatsappRedirect(UmkmProfile $umkm, string $message): RedirectResponse
    {
        $number = preg_replace('/[^0-9]/', '', $umkm->whatsapp);

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return redirect()->away('https://wa.me/' . $number . '?text=' . rawurlencode($message));
    }
}
