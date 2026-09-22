<?php

namespace Tests\Feature;

use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_umkm_profile_edit_screen_can_be_rendered(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id'       => $user->id,
            'business_name' => 'Usaha Maju Bersama',
            'owner_name'    => 'Budi Santoso',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        $response = $this->actingAs($user)->get('/profil-usaha');

        $response->assertOk();
        $response->assertSee('Tahun Pendirian UMKM');
        $response->assertSee('Jumlah Karyawan');
        $response->assertSee('Kelurahan / Desa');
        $response->assertSee('Sertifikasi Halal');
        $response->assertSee('Pernah Mengikuti Pelatihan dan Pendampingan Kewirausahaan UMKM?');
        $response->assertDontSee('Status Afiliasi');
    }

    public function test_umkm_profile_can_be_updated_with_new_fields(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        $profile = UmkmProfile::create([
            'user_id'       => $user->id,
            'business_name' => 'Usaha Maju Bersama',
            'owner_name'    => 'Budi Santoso',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        $response = $this->actingAs($user)->put('/profil-usaha', [
            'business_name'          => 'Usaha Berkah Maju',
            'owner_name'             => 'Budi Santoso S.E.',
            'established_year'       => 2019,
            'employee_count'         => 8,
            'description'            => 'Deskripsi profil usaha terupdate',
            'address'                => 'Jl. Malioboro No. 12',
            'kelurahan'              => 'Suryatmajan',
            'kecamatan'              => 'Danurejan',
            'kabupaten_kota'         => 'Kota Yogyakarta',
            'whatsapp'               => '081299998888',
            'instagram'              => '@usaha_berkah',
            'nib'                    => '9120009999999',
            'has_halal_certificate'  => 1,
            'halal_certificate_year' => 2021,
            'has_attended_training'  => 'ya',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profil-usaha');

        $profile->refresh();

        $this->assertSame('Usaha Berkah Maju', $profile->business_name);
        $this->assertSame(2019, $profile->established_year);
        $this->assertSame(8, $profile->employee_count);
        $this->assertSame('Suryatmajan', $profile->kelurahan);
        $this->assertSame('081299998888', $profile->whatsapp);
        $this->assertTrue($profile->has_halal_certificate);
        $this->assertSame(2021, $profile->halal_certificate_year);
        $this->assertSame('ya', $profile->has_attended_training);
    }

    public function test_whatsapp_must_be_numeric(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id'       => $user->id,
            'business_name' => 'Usaha Maju Bersama',
            'owner_name'    => 'Budi Santoso',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        $response = $this->actingAs($user)->put('/profil-usaha', [
            'business_name'         => 'Usaha Berkah Maju',
            'owner_name'            => 'Budi Santoso',
            'whatsapp'              => 'nomor-bukan-angka',
            'has_halal_certificate' => 0,
            'has_attended_training' => 'tidak',
        ]);

        $response->assertSessionHasErrors('whatsapp');
    }

    public function test_halal_year_is_required_if_has_halal_certificate(): void
    {
        $user = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id'       => $user->id,
            'business_name' => 'Usaha Maju Bersama',
            'owner_name'    => 'Budi Santoso',
            'whatsapp'      => '081234567890',
            'status'        => 'approved',
        ]);

        $response = $this->actingAs($user)->put('/profil-usaha', [
            'business_name'          => 'Usaha Berkah Maju',
            'owner_name'             => 'Budi Santoso',
            'whatsapp'               => '081234567890',
            'has_halal_certificate'  => 1,
            'halal_certificate_year' => null,
            'has_attended_training'  => 'tidak',
        ]);

        $response->assertSessionHasErrors('halal_certificate_year');
    }

    public function test_admin_can_filter_umkm_by_training_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $user1 = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id'               => $user1->id,
            'business_name'         => 'UMKM Pernah Pelatihan',
            'owner_name'            => 'Budi Santoso',
            'whatsapp'              => '081234567890',
            'status'                => 'approved',
            'has_attended_training' => 'ya',
        ]);

        $user2 = User::factory()->create(['role' => 'umkm']);
        UmkmProfile::create([
            'user_id'               => $user2->id,
            'business_name'         => 'UMKM Belum Pelatihan',
            'owner_name'            => 'Siti Aminah',
            'whatsapp'              => '081234567891',
            'status'                => 'approved',
            'has_attended_training' => 'tidak',
        ]);

        // Filter: 'ya'
        $responseYa = $this->actingAs($admin)->get('/admin/umkm?has_attended_training=ya');
        $responseYa->assertOk();
        $responseYa->assertSee('UMKM Pernah Pelatihan');
        $responseYa->assertDontSee('UMKM Belum Pelatihan');

        // Filter: 'tidak'
        $responseTidak = $this->actingAs($admin)->get('/admin/umkm?has_attended_training=tidak');
        $responseTidak->assertOk();
        $responseTidak->assertSee('UMKM Belum Pelatihan');
        $responseTidak->assertDontSee('UMKM Pernah Pelatihan');
    }
}
