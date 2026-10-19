<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappContactTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $umkmStatus = 'approved', string $productStatus = 'active'): Product
    {
        $owner = User::factory()->create(['role' => 'umkm']);
        $profile = UmkmProfile::create([
            'user_id'       => $owner->id,
            'business_name' => 'Usaha Contoh',
            'owner_name'    => 'Pemilik',
            'whatsapp'      => '081234567890',
            'status'        => $umkmStatus,
        ]);
        $category = Category::create(['name' => 'Mikro', 'slug' => 'mikro']);

        return Product::create([
            'umkm_id'     => $profile->id,
            'category_id' => $category->id,
            'name'        => 'Produk Contoh',
            'status'      => $productStatus,
        ]);
    }

    public function test_guest_does_not_see_whatsapp_link_or_number_on_product_page(): void
    {
        $product = $this->product();

        $this->get("/produk-publik/{$product->id}")
            ->assertOk()
            ->assertSee('Login untuk Memesan via WhatsApp')
            ->assertDontSee('wa.me')
            ->assertDontSee('081234567890')
            ->assertDontSee('6281234567890');
    }

    public function test_guest_does_not_see_whatsapp_link_on_umkm_page(): void
    {
        $product = $this->product();

        $this->get("/katalog/{$product->umkm_id}")
            ->assertOk()
            ->assertSee('Login untuk Memesan via WhatsApp')
            ->assertDontSee('wa.me');
    }

    public function test_guest_is_sent_to_login_when_opening_contact_route(): void
    {
        $product = $this->product();

        $this->get("/produk-publik/{$product->id}/hubungi")->assertRedirect('/login');
        $this->get("/katalog/{$product->umkm_id}/hubungi")->assertRedirect('/login');
    }

    public function test_logged_in_user_sees_button_and_is_redirected_to_whatsapp(): void
    {
        $product = $this->product();
        $visitor = User::factory()->create(['role' => 'umkm']);

        $this->actingAs($visitor)->get("/produk-publik/{$product->id}")
            ->assertOk()
            ->assertSee('Pesan via WhatsApp')
            ->assertSee("/produk-publik/{$product->id}/hubungi", false);

        $response = $this->actingAs($visitor)->get("/produk-publik/{$product->id}/hubungi");
        $response->assertRedirectContains('https://wa.me/6281234567890?text=');

        $this->actingAs($visitor)->get("/katalog/{$product->umkm_id}/hubungi")
            ->assertRedirectContains('https://wa.me/6281234567890?text=');
    }

    public function test_contact_route_hidden_for_unapproved_umkm(): void
    {
        $product = $this->product('pending');
        $visitor = User::factory()->create(['role' => 'umkm']);

        $this->actingAs($visitor)->get("/produk-publik/{$product->id}/hubungi")->assertNotFound();
        $this->actingAs($visitor)->get("/katalog/{$product->umkm_id}/hubungi")->assertNotFound();
    }
}
