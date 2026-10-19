<?php

namespace Tests\Feature;

use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmApprovalHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function pendingProfile(string $name = 'Usaha Menunggu'): UmkmProfile
    {
        $user = User::factory()->create(['role' => 'umkm']);

        return UmkmProfile::create([
            'user_id'       => $user->id,
            'business_name' => $name,
            'owner_name'    => 'Pemilik',
            'whatsapp'      => '081234567890',
            'status'        => 'pending',
        ]);
    }

    public function test_reject_requires_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $profile = $this->pendingProfile();

        $response = $this->actingAs($admin)
            ->from('/admin/persetujuan-umkm')
            ->post("/admin/persetujuan-umkm/{$profile->id}/reject", ['rejection_reason' => '']);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertSame('pending', $profile->fresh()->status);
    }

    public function test_reject_stores_reason_reviewer_and_time(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $profile = $this->pendingProfile();

        $this->actingAs($admin)->post("/admin/persetujuan-umkm/{$profile->id}/reject", [
            'rejection_reason' => 'Nomor WhatsApp tidak dapat dihubungi.',
        ])->assertSessionHasNoErrors();

        $profile->refresh();
        $this->assertSame('rejected', $profile->status);
        $this->assertSame('Nomor WhatsApp tidak dapat dihubungi.', $profile->rejection_reason);
        $this->assertSame($admin->id, $profile->reviewed_by);
        $this->assertNotNull($profile->reviewed_at);
    }

    public function test_approve_records_reviewer_and_clears_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $profile = $this->pendingProfile();
        $profile->update(['rejection_reason' => 'alasan lama yang sudah tidak berlaku']);

        $this->actingAs($admin)->post("/admin/persetujuan-umkm/{$profile->id}/approve");

        $profile->refresh();
        $this->assertSame('approved', $profile->status);
        $this->assertNull($profile->rejection_reason);
        $this->assertSame($admin->id, $profile->reviewed_by);
        $this->assertNotNull($profile->reviewed_at);
    }

    public function test_history_shows_decisions_and_rejection_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rejected = $this->pendingProfile('Usaha Ditolak');
        $approved = $this->pendingProfile('Usaha Disetujui');
        $untouched = $this->pendingProfile('Usaha Belum Ditinjau');

        $this->actingAs($admin)->post("/admin/persetujuan-umkm/{$rejected->id}/reject", [
            'rejection_reason' => 'Alamat usaha tidak jelas dan tidak bisa diverifikasi.',
        ]);
        $this->actingAs($admin)->post("/admin/persetujuan-umkm/{$approved->id}/approve");

        $page = $this->actingAs($admin)->get('/admin/persetujuan-umkm');
        $page->assertOk();
        $page->assertSee('Histori Persetujuan');
        $page->assertSee('Alamat usaha tidak jelas dan tidak bisa diverifikasi.');

        $filtered = $this->actingAs($admin)->get('/admin/persetujuan-umkm?history_status=rejected');
        $filtered->assertSee('Alamat usaha tidak jelas');
        $this->assertCount(1, $filtered->viewData('history'));
        $this->assertSame($rejected->id, $filtered->viewData('history')->first()->id);
    }

    public function test_umkm_sees_rejection_reason_on_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $profile = $this->pendingProfile();

        $this->actingAs($admin)->post("/admin/persetujuan-umkm/{$profile->id}/reject", [
            'rejection_reason' => 'Logo dan deskripsi usaha belum sesuai ketentuan.',
        ]);

        $this->actingAs($profile->user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Logo dan deskripsi usaha belum sesuai ketentuan.');
    }

    public function test_non_admin_cannot_reject(): void
    {
        $umkm = User::factory()->create(['role' => 'umkm']);
        $profile = $this->pendingProfile();

        $this->actingAs($umkm)->post("/admin/persetujuan-umkm/{$profile->id}/reject", [
            'rejection_reason' => 'Percobaan tanpa hak akses.',
        ])->assertForbidden();
    }
}
