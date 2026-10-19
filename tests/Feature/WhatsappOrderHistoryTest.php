<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\UmkmProfile;
use App\Models\User;
use App\Models\WhatsappOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $name = 'Bakpia Kacang Hijau', string $business = 'Bakpia Menoreh'): Product
    {
        $owner = User::factory()->create(['role' => 'umkm']);
        $profile = UmkmProfile::create([
            'user_id'       => $owner->id,
            'business_name' => $business,
            'owner_name'    => 'Pemilik',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        return Product::create([
            'umkm_id'     => $profile->id,
            'category_id' => Category::firstOrCreate(['slug' => 'mikro'], ['name' => 'Mikro'])->id,
            'name'        => $name,
            'status'      => 'active',
        ]);
    }

    private function visitor(string $name = 'Budi Pembeli'): User
    {
        return User::factory()->create(['role' => 'pengunjung', 'name' => $name]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/riwayat')->assertRedirect('/login');
    }

    public function test_pressing_order_button_on_product_is_recorded(): void
    {
        $product = $this->product();
        $visitor = $this->visitor();

        $this->actingAs($visitor)->get("/produk-publik/{$product->id}/hubungi")->assertRedirectContains('https://wa.me/');

        $this->assertDatabaseHas('whatsapp_orders', [
            'user_id'       => $visitor->id,
            'umkm_id'       => $product->umkm_id,
            'product_id'    => $product->id,
            'business_name' => 'Bakpia Menoreh',
            'product_name'  => 'Bakpia Kacang Hijau',
        ]);
    }

    public function test_pressing_order_button_on_umkm_page_is_recorded_without_product(): void
    {
        $product = $this->product();
        $visitor = $this->visitor();

        $this->actingAs($visitor)->get("/katalog/{$product->umkm_id}/hubungi");

        $this->assertDatabaseHas('whatsapp_orders', [
            'user_id'      => $visitor->id,
            'umkm_id'      => $product->umkm_id,
            'product_id'   => null,
            'product_name' => null,
        ]);
    }

    public function test_blocked_or_unavailable_target_is_not_recorded(): void
    {
        $product = $this->product();
        $product->update(['status' => 'inactive']);

        $this->actingAs($this->visitor())->get("/produk-publik/{$product->id}/hubungi")->assertNotFound();

        $this->assertDatabaseCount('whatsapp_orders', 0);
    }

    public function test_history_page_lists_only_own_entries_newest_first(): void
    {
        $visitor = $this->visitor();
        $other = $this->visitor('Orang Lain');
        $first = $this->product('Produk Lama', 'Usaha Lama');
        $second = $this->product('Produk Baru', 'Usaha Baru');
        $secret = $this->product('Produk Orang Lain', 'Usaha Rahasia');

        WhatsappOrder::create(['user_id' => $visitor->id, 'umkm_id' => $first->umkm_id, 'product_id' => $first->id, 'business_name' => 'Usaha Lama', 'product_name' => 'Produk Lama', 'created_at' => now()->subDay()]);
        WhatsappOrder::create(['user_id' => $visitor->id, 'umkm_id' => $second->umkm_id, 'product_id' => $second->id, 'business_name' => 'Usaha Baru', 'product_name' => 'Produk Baru']);
        WhatsappOrder::create(['user_id' => $other->id, 'umkm_id' => $secret->umkm_id, 'product_id' => $secret->id, 'business_name' => 'Usaha Rahasia', 'product_name' => 'Produk Orang Lain']);

        $response = $this->actingAs($visitor)->get('/riwayat')->assertOk();

        $response->assertSeeInOrder(['Produk Baru', 'Produk Lama']);
        $response->assertDontSee('Produk Orang Lain');
        $response->assertDontSee('Usaha Rahasia');
        $response->assertSee("/produk-publik/{$second->id}/hubungi", false);
    }

    public function test_deleted_product_stays_in_history_and_falls_back_to_umkm_contact(): void
    {
        $product = $this->product('Produk Dihapus', 'Usaha Aktif');
        $visitor = $this->visitor();
        $umkmId = $product->umkm_id;

        $this->actingAs($visitor)->get("/produk-publik/{$product->id}/hubungi");
        $product->delete();

        $this->actingAs($visitor)->get('/riwayat')
            ->assertOk()
            ->assertSee('Produk Dihapus')
            ->assertSee('Usaha Aktif')
            ->assertSee("/katalog/{$umkmId}/hubungi", false)
            ->assertDontSee('Tidak tersedia lagi');
    }

    public function test_entry_is_unavailable_when_umkm_is_no_longer_public(): void
    {
        $product = $this->product('Produk Ditarik', 'Usaha Disuspend');
        $visitor = $this->visitor();

        $this->actingAs($visitor)->get("/produk-publik/{$product->id}/hubungi");
        $product->umkmProfile->user->update(['is_active' => false]);

        $this->actingAs($visitor)->get('/riwayat')
            ->assertOk()
            ->assertSee('Produk Ditarik')
            ->assertSee('Tidak tersedia lagi')
            ->assertDontSee('Pesan lagi');
    }

    public function test_empty_state(): void
    {
        $this->actingAs($this->visitor())->get('/riwayat')->assertOk()->assertSee('Belum ada riwayat pesanan.');
    }

    public function test_account_dropdown_links_to_history(): void
    {
        $this->actingAs($this->visitor())->get('/')
            ->assertSee('Riwayat Pesanan')
            ->assertSee('href="' . route('riwayat.index') . '"', false);
    }

    public function test_guest_login_flow_records_the_click_after_login(): void
    {
        $product = $this->product();
        $visitor = $this->visitor();
        $visitor->update(['password' => 'password']);

        $this->get("/produk-publik/{$product->id}/hubungi")->assertRedirect('/login');
        $this->post('/login', ['email' => $visitor->email, 'password' => 'password'])
            ->assertRedirect(url("/produk-publik/{$product->id}/hubungi"));
        $this->get("/produk-publik/{$product->id}/hubungi");

        $this->assertDatabaseCount('whatsapp_orders', 1);
    }
}
