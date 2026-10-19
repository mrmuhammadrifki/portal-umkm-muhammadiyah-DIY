<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengunjungRoleTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        $owner = User::factory()->create(['role' => 'umkm']);
        $profile = UmkmProfile::create([
            'user_id'       => $owner->id,
            'business_name' => 'Usaha Contoh',
            'owner_name'    => 'Pemilik',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        return Product::create([
            'umkm_id'     => $profile->id,
            'category_id' => Category::create(['name' => 'Mikro', 'slug' => 'mikro'])->id,
            'name'        => 'Produk Contoh',
            'status'      => 'active',
        ]);
    }

    private function visitor(): User
    {
        return User::factory()->create(['role' => 'pengunjung', 'name' => 'Budi Pembeli']);
    }

    public function test_registration_form_renders(): void
    {
        $this->get('/register/pengunjung')->assertOk()->assertSee('Daftar sebagai Pengunjung');
    }

    public function test_pengunjung_can_register_without_umkm_profile(): void
    {
        $response = $this->post('/register/pengunjung', [
            'name'                  => 'Budi Pembeli',
            'email'                 => 'budi@example.com',
            'phone'                 => '081299990000',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('katalog.publik'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertSame('pengunjung', $user->role);
        $this->assertNull($user->umkmProfile);
        $this->assertSame(0, UmkmProfile::count());
    }

    public function test_registration_cannot_choose_role(): void
    {
        $this->post('/register/pengunjung', [
            'name'                  => 'Nakal',
            'email'                 => 'nakal@example.com',
            'phone'                 => '081299990001',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role'                  => 'admin',
        ]);

        $this->assertSame('pengunjung', User::where('email', 'nakal@example.com')->value('role'));
    }

    public function test_registration_requires_valid_whatsapp(): void
    {
        $this->post('/register/pengunjung', [
            'name'                  => 'Budi',
            'email'                 => 'budi2@example.com',
            'phone'                 => 'abc',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('phone');
    }

    public function test_after_register_visitor_returns_to_order_flow(): void
    {
        $product = $this->product();

        // Guest klik "pesan" → dilempar ke login, URL tujuan tersimpan
        $this->get("/produk-publik/{$product->id}/hubungi")->assertRedirect('/login');

        $this->post('/register/pengunjung', [
            'name'                  => 'Budi Pembeli',
            'email'                 => 'budi3@example.com',
            'phone'                 => '081299990002',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(url("/produk-publik/{$product->id}/hubungi"));
    }

    public function test_pengunjung_order_message_contains_buyer_name_and_product(): void
    {
        $product = $this->product();

        $response = $this->actingAs($this->visitor())->get("/produk-publik/{$product->id}/hubungi");

        $response->assertRedirectContains('https://wa.me/6281234567890?text=');
        $location = urldecode($response->headers->get('Location'));
        $this->assertStringContainsString('Budi Pembeli', $location);
        $this->assertStringContainsString('memesan produk Produk Contoh', $location);
    }

    public function test_pengunjung_dashboard_redirects_to_catalog(): void
    {
        $this->actingAs($this->visitor())->get('/dashboard')->assertRedirect(route('katalog.publik'));
    }

    public function test_pengunjung_cannot_access_umkm_or_admin_areas(): void
    {
        $visitor = $this->visitor();

        $this->actingAs($visitor)->get('/produk')->assertForbidden();
        $this->actingAs($visitor)->get('/profil-usaha')->assertForbidden();
        $this->actingAs($visitor)->get('/admin/persetujuan-umkm')->assertForbidden();
    }

    public function test_pengunjung_can_open_account_profile_page(): void
    {
        $this->actingAs($this->visitor())->get('/profile')->assertOk();
    }

    public function test_umkm_still_has_access_to_own_areas(): void
    {
        $umkm = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id' => $umkm->id, 'business_name' => 'U', 'owner_name' => 'O',
            'whatsapp' => '081234567890', 'status' => 'approved',
        ]);

        $this->actingAs($umkm)->get('/produk')->assertOk();
        $this->actingAs($umkm)->get('/profil-usaha')->assertOk();
    }

    public function test_public_navbar_shows_login_for_guest(): void
    {
        $this->get('/')
            ->assertSee('href="' . route('login') . '"', false)
            ->assertSee('Daftar UMKM')
            ->assertDontSee('Logout');
    }

    public function test_pengunjung_navbar_has_account_dropdown_and_daftar_umkm_button(): void
    {
        $html = $this->actingAs($this->visitor())->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Budi Pembeli', $html);
        $this->assertStringContainsString('href="' . route('profile.edit') . '"', $html);
        $this->assertStringContainsString('Logout', $html);
        // tombol Daftar UMKM berada di sebelah kanan nama akun
        $this->assertGreaterThan(
            strpos($html, 'Budi Pembeli'),
            strpos($html, 'Daftar UMKM', strpos($html, 'Budi Pembeli'))
        );
        $this->assertStringNotContainsString('href="' . route('login') . '"', $html);
    }

    public function test_umkm_navbar_has_dropdown_without_daftar_umkm_button(): void
    {
        $umkm = User::factory()->create(['role' => 'umkm', 'name' => 'Pemilik Usaha']);

        $this->actingAs($umkm)->get('/')
            ->assertSee('Pemilik Usaha')
            ->assertSee('Logout')
            ->assertDontSee('Daftar UMKM Sekarang')
            ->assertSee('Dashboard');
    }

    public function test_pengunjung_can_register_a_business(): void
    {
        $visitor = $this->visitor();

        $this->actingAs($visitor)->get('/register')->assertOk();

        $this->actingAs($visitor)->post('/register', [
            'name'                  => 'Pemilik Baru',
            'email'                 => 'pemilik.baru@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'business_name'         => 'Usaha Baru',
            'whatsapp'              => '081234567899',
        ])->assertRedirect(route('dashboard'));

        $newUser = User::where('email', 'pemilik.baru@example.com')->first();
        $this->assertSame('umkm', $newUser->role);
        $this->assertAuthenticatedAs($newUser);
    }

    public function test_umkm_and_admin_are_redirected_away_from_register(): void
    {
        foreach (['umkm', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/register')->assertRedirect(route('dashboard'));
        }
    }
}
