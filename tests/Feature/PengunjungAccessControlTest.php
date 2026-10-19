<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Memastikan akun role `pengunjung` tidak punya akses ke fitur admin maupun UMKM.
 */
class PengunjungAccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Route ber-auth yang memang boleh dibuka pengunjung. Route auth baru yang
     * tidak ada di sini dan tidak dijaga middleware admin / role:umkm akan
     * menggagalkan test_every_authenticated_route_is_guarded_or_explicitly_allowed.
     */
    private const ALLOWED_FOR_PENGUNJUNG = [
        'dashboard',                  // dialihkan ke katalog
        'logout',
        'profile.edit', 'profile.update', 'profile.destroy',
        'password.update', 'password.confirm', 'verification.notice', 'verification.verify', 'verification.send',
        'katalog.hubungi', 'katalog.produk-hubungi',   // pesan via WhatsApp
        'confirm-password',           // POST bawaan Breeze (tanpa nama), dikenali lewat URI
    ];

    private function visitor(): User
    {
        return User::factory()->create(['role' => 'pengunjung']);
    }

    private function pendingProfile(): UmkmProfile
    {
        $owner = User::factory()->create(['role' => 'umkm']);

        return UmkmProfile::create([
            'user_id'       => $owner->id,
            'business_name' => 'Usaha Target',
            'owner_name'    => 'Pemilik',
            'whatsapp'      => '081234567890',
            'status'        => 'pending',
        ]);
    }

    public function test_every_authenticated_route_is_guarded_or_explicitly_allowed(): void
    {
        $unguarded = [];

        foreach (Route::getRoutes() as $route) {
            $middleware = $route->gatherMiddleware();
            $requiresAuth = collect($middleware)->contains(fn ($m) => is_string($m) && ($m === 'auth' || str_starts_with($m, 'auth:') || str_contains($m, 'Authenticate')));

            if (! $requiresAuth || str_starts_with($route->uri(), 'api/')) {
                continue;
            }

            $guarded = in_array('admin', $middleware, true)
                || collect($middleware)->contains(fn ($m) => is_string($m) && str_starts_with($m, 'role:'));

            if (! $guarded && ! in_array($route->getName(), self::ALLOWED_FOR_PENGUNJUNG, true) && ! in_array($route->uri(), self::ALLOWED_FOR_PENGUNJUNG, true)) {
                $unguarded[] = $route->methods()[0] . ' /' . $route->uri() . ' (' . ($route->getName() ?? 'tanpa nama') . ')';
            }
        }

        $this->assertSame([], $unguarded, "Route ber-auth tanpa pembatas role:\n" . implode("\n", $unguarded));
    }

    public function test_all_admin_and_umkm_routes_are_blocked_for_pengunjung(): void
    {
        $visitor = $this->visitor();
        $checked = 0;

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            $isAdmin = str_starts_with($uri, 'admin/');
            $isUmkmArea = $uri === 'profil-usaha' || $uri === 'produk' || str_starts_with($uri, 'produk/');

            if (! $isAdmin && ! $isUmkmArea) {
                continue;
            }

            $path = '/' . preg_replace('/\{[^}]+\}/', '1', $uri);
            $method = $route->methods()[0];

            $status = $this->actingAs($visitor)->call($method, $path)->getStatusCode();

            $this->assertContains(
                $status,
                [403, 404],
                "{$method} {$path} seharusnya ditolak untuk pengunjung, tetapi mengembalikan {$status}"
            );

            // Route tanpa parameter harus tegas 403 (bukan 404 karena model tak ditemukan)
            if (! str_contains($uri, '{')) {
                $this->assertSame(403, $status, "{$method} {$path} harus 403 untuk pengunjung");
            }

            $checked++;
        }

        $this->assertGreaterThan(30, $checked);
    }

    public function test_pengunjung_cannot_change_data_through_admin_actions(): void
    {
        $visitor = $this->visitor();
        $profile = $this->pendingProfile();

        $this->actingAs($visitor)->post("/admin/persetujuan-umkm/{$profile->id}/approve")->assertForbidden();
        $this->actingAs($visitor)->post("/admin/persetujuan-umkm/{$profile->id}/reject", ['rejection_reason' => 'Percobaan menolak tanpa hak.'])->assertForbidden();
        $this->actingAs($visitor)->post("/admin/umkm/{$profile->id}/suspend")->assertForbidden();
        $this->actingAs($visitor)->post('/admin/kategori', ['name' => 'Kategori Palsu'])->assertForbidden();
        $this->actingAs($visitor)->post('/admin/umkm/import', [
            'file' => UploadedFile::fake()->create('data.xlsx', 10),
        ])->assertForbidden();

        $profile->refresh();
        $this->assertSame('pending', $profile->status);
        $this->assertNull($profile->reviewed_at);
        $this->assertTrue($profile->user->is_active);
        $this->assertSame(0, Category::count());
    }

    public function test_pengunjung_cannot_manage_products_or_business_profile(): void
    {
        $visitor = $this->visitor();
        $category = Category::create(['name' => 'Mikro', 'slug' => 'mikro']);
        $profile = $this->pendingProfile();
        $product = Product::create([
            'umkm_id' => $profile->id, 'category_id' => $category->id, 'name' => 'Produk Asli', 'status' => 'active',
        ]);

        $this->actingAs($visitor)->post('/produk', [
            'category_id' => $category->id, 'name' => 'Produk Palsu', 'status' => 'active',
        ])->assertForbidden();
        $this->actingAs($visitor)->put("/produk/{$product->id}", [
            'category_id' => $category->id, 'name' => 'Diubah', 'status' => 'inactive',
        ])->assertForbidden();
        $this->actingAs($visitor)->delete("/produk/{$product->id}")->assertForbidden();
        $this->actingAs($visitor)->put('/profil-usaha', ['business_name' => 'Diubah'])->assertForbidden();

        $this->assertSame(1, Product::count());
        $this->assertSame('Produk Asli', $product->fresh()->name);
        $this->assertSame('Usaha Target', $profile->fresh()->business_name);
    }

    public function test_pengunjung_cannot_escalate_role_through_profile_update(): void
    {
        $visitor = $this->visitor();

        $this->actingAs($visitor)->patch('/profile', [
            'name'  => 'Nama Baru',
            'email' => $visitor->email,
            'role'  => 'admin',
        ]);

        $visitor->refresh();
        $this->assertSame('Nama Baru', $visitor->name);
        $this->assertSame('pengunjung', $visitor->role);
    }

    public function test_navbar_and_nav_menu_do_not_expose_admin_or_umkm_links_to_pengunjung(): void
    {
        $html = $this->actingAs($this->visitor())->get('/profile')->assertOk()->getContent();

        foreach (['produk.index', 'umkm-profile.edit', 'admin.umkm-profiles.pending', 'admin.umkm-profiles.index', 'admin.event.index'] as $name) {
            $this->assertStringNotContainsString('href="' . route($name) . '"', $html, "Link {$name} tidak boleh tampil untuk pengunjung");
        }
        $this->assertStringNotContainsString('Persetujuan', $html);
        $this->assertStringNotContainsString('Produk Saya', $html);
        $this->assertStringNotContainsString('Profil Usaha', $html);
    }
}
