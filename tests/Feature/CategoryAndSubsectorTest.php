<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subsector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAndSubsectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_cannot_access_category_page(): void
    {
        $response = $this->get('/admin/kategori');
        $response->assertRedirect('/login');
    }

    public function test_regular_umkm_cannot_access_category_page(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        $response = $this->actingAs($user)->get('/admin/kategori');
        $response->assertForbidden();
    }

    public function test_admin_can_view_category_and_subsector_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/kategori');

        $response->assertOk();
        $response->assertSee('Usaha Mikro');
        $response->assertSee('Usaha Kecil');
        $response->assertSee('Usaha Menengah');
        $response->assertSee('Subsektor');
        $response->assertSee('Kuliner');
        $response->assertSee('Fashion');
        $response->assertSee('Kriya (Kerajinan Tangan)');
    }

    public function test_admin_can_create_new_subsector(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/subsektor', [
            'name'        => 'Subsektor Khusus Baru',
            'icon'        => '🚀',
            'description' => 'Subsektor baru untuk pengujian fungsionalitas sistem.',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('subsectors', [
            'name' => 'Subsektor Khusus Baru',
            'slug' => 'subsektor-khusus-baru',
            'icon' => '🚀',
        ]);
    }

    public function test_admin_can_update_subsector(): void
    {
        $admin = User::where('role', 'admin')->first();
        $subsector = Subsector::first();

        $response = $this->actingAs($admin)->put("/admin/subsektor/{$subsector->id}", [
            'name'        => $subsector->name . ' Diperbarui',
            'icon'        => '✨',
            'description' => 'Deskripsi diperbarui',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('subsectors', [
            'id'   => $subsector->id,
            'name' => $subsector->name . ' Diperbarui',
            'icon' => '✨',
        ]);
    }

    public function test_admin_can_update_category_thresholds(): void
    {
        $admin = User::where('role', 'admin')->first();
        $mikro = Category::where('slug', 'mikro')->first();

        $response = $this->actingAs($admin)->put("/admin/kategori/{$mikro->id}", [
            'name'        => 'Usaha Mikro Mandiri',
            'min_revenue' => 0,
            'max_revenue' => 30000000,
            'description' => 'Threshold baru mikro sampai 30 juta.',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('categories', [
            'id'          => $mikro->id,
            'name'        => 'Usaha Mikro Mandiri',
            'max_revenue' => 30000000,
        ]);
    }
}
