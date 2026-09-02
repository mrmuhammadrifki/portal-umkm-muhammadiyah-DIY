<?php
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UmkmProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Jalur halaman depan publik untuk pengunjung umum
Route::get('/', [UmkmController::class, 'halamanUtama'])->name('katalog.publik');

// Jalur halaman detail UMKM untuk publik
Route::get('/katalog/{id}', [UmkmController::class, 'detail'])->name('katalog.detail');

// Katalog produk publik (F9, F10, F12)
Route::get('/produk-publik', [UmkmController::class, 'produkPublik'])->name('katalog.produk');
Route::get('/produk-publik/{product}', [UmkmController::class, 'produkDetail'])->name('katalog.produk-detail');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil usaha milik UMKM yang sedang login (satu profil per user)
    Route::get('/profil-usaha', [UmkmProfileController::class, 'edit'])->name('umkm-profile.edit');
    Route::put('/profil-usaha', [UmkmProfileController::class, 'update'])->name('umkm-profile.update');

    // Produk milik UMKM yang sedang login
    Route::get('/produk', [ProductController::class, 'index'])->name('produk.index');
    Route::get('/produk/tambah', [ProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProductController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [ProductController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [ProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{produk}', [ProductController::class, 'destroy'])->name('produk.destroy');
    Route::delete('/produk/{produk}/foto/{image}', [ProductController::class, 'destroyImage'])->name('produk.image.destroy');

    // Rute Profil Bawaan Breeze yang dicari oleh sistem
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/persetujuan-umkm', [UmkmProfileController::class, 'pending'])->name('umkm-profiles.pending');
    Route::post('/persetujuan-umkm/{umkmProfile}/approve', [UmkmProfileController::class, 'approve'])->name('umkm-profiles.approve');
    Route::post('/persetujuan-umkm/{umkmProfile}/reject', [UmkmProfileController::class, 'reject'])->name('umkm-profiles.reject');

    // F14: listing + search semua UMKM/produk untuk moderasi
    Route::get('/umkm', [UmkmProfileController::class, 'index'])->name('umkm-profiles.index');
    Route::get('/produk', [ProductController::class, 'adminIndex'])->name('produk.index');

    // F15: suspend / aktifkan kembali akun UMKM bermasalah
    Route::post('/umkm/{umkmProfile}/suspend', [UmkmProfileController::class, 'suspend'])->name('umkm-profiles.suspend');
    Route::post('/umkm/{umkmProfile}/reactivate', [UmkmProfileController::class, 'reactivate'])->name('umkm-profiles.reactivate');

    Route::resource('kategori', CategoryController::class)->except(['show']);
});

require __DIR__.'/auth.php';
