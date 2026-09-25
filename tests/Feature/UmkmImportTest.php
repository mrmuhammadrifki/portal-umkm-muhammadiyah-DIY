<?php

namespace Tests\Feature;

use App\Models\Subsector;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class UmkmImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_regular_umkm_cannot_download_template(): void
    {
        // Guest
        $response = $this->get(route('admin.umkm.import.template'));
        $response->assertRedirect(route('login'));

        // Regular UMKM
        $umkm = User::factory()->create(['role' => 'umkm']);
        $response = $this->actingAs($umkm)->get(route('admin.umkm.import.template'));
        $response->assertStatus(403);
    }

    public function test_admin_can_download_excel_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.umkm.import.template'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_import_umkm_data_from_excel_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $subsector = Subsector::create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        // Create an in-memory Excel file using PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'Nama Usaha *', 'Nama Pemilik *', 'Email Akun *', 'Password Akun',
            'Nomor WhatsApp *', 'Subsektor EKRAF', 'Kabupaten / Kota',
            'Kecamatan', 'Kelurahan / Desa', 'Alamat Lengkap', 'Tahun Berdiri',
            'Jumlah Karyawan', 'Omzet Bulanan (Rp)', 'NIB',
            'Sertifikat Halal (Ya/Tidak)', 'Tahun Sertifikat Halal',
            'Pernah Pelatihan (Ya/Tidak)', 'Instagram', 'Deskripsi Usaha',
            'Status Moderasi'
        ];
        $sheet->fromArray([$headers], null, 'A1');

        // Row 2: UMKM 1
        $row2 = [
            'Warung Soto Berkah', 'H. Ahmad', 'soto.berkah@example.com', 'secret123',
            '081234567890', 'Kuliner', 'Kota Yogyakarta',
            'Danurejan', 'Bausasran', 'Jl. Hayam Wuruk No. 10', '2019',
            '4', '15000000', '9120001234567',
            'Ya', '2022',
            'Ya', '@sotoberkah_jogja', 'Soto ayam kampung gurih khas Jogja.',
            'Approved'
        ];
        $sheet->fromArray([$row2], null, 'A2');

        // Row 3: UMKM 2
        $row3 = [
            'Kopi Merapi Jaya', 'Pak Maryanto', 'kopi.merapi.test@example.com', '',
            '081987654321', 'Kuliner', 'Kabupaten Sleman',
            'Pakem', 'Hargobinangun', 'Jl. Kaliurang KM 20', '2021',
            '6', '35000000', '9120002345678',
            'Tidak', '',
            'Tidak', '@kopimerapi_test', 'Biji kopi robusta dan arabika lereng merapi.',
            'Approved'
        ];
        $sheet->fromArray([$row3], null, 'A3');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_umkm_import_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile($tempPath, 'data_umkm.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($admin)->post(route('admin.umkm.import'), [
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect(route('admin.umkm-profiles.index'));
        $response->assertSessionHas('success');

        // Verify User 1 & Profile 1
        $this->assertDatabaseHas('users', [
            'email' => 'soto.berkah@example.com',
            'role'  => 'umkm',
            'phone' => '081234567890',
        ]);
        $this->assertDatabaseHas('umkm_profiles', [
            'business_name'         => 'Warung Soto Berkah',
            'owner_name'            => 'H. Ahmad',
            'kabupaten_kota'        => 'Kota Yogyakarta',
            'has_halal_certificate' => true,
            'halal_certificate_year'=> 2022,
            'has_attended_training' => 'ya',
            'subsector_id'          => $subsector->id,
            'status'                => 'approved',
        ]);

        // Verify User 2 & Profile 2
        $this->assertDatabaseHas('users', [
            'email' => 'kopi.merapi.test@example.com',
            'role'  => 'umkm',
        ]);
        $this->assertDatabaseHas('umkm_profiles', [
            'business_name'         => 'Kopi Merapi Jaya',
            'owner_name'            => 'Pak Maryanto',
            'kabupaten_kota'        => 'Kabupaten Sleman',
            'has_halal_certificate' => false,
            'has_attended_training' => 'tidak',
            'status'                => 'approved',
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_admin_can_download_sample_data_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.umkm.import.sample'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_importing_actual_sample_data_file_succeeds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Pastikan subsektor referensi ada
        Subsector::firstOrCreate(['slug' => 'kuliner'], ['name' => 'Kuliner']);
        Subsector::firstOrCreate(['slug' => 'fashion'], ['name' => 'Fashion']);
        Subsector::firstOrCreate(['slug' => 'kriya'], ['name' => 'Kriya (Kerajinan Tangan)']);

        $samplePath = public_path('templates/contoh-data-import-umkm.xlsx');
        $this->assertFileExists($samplePath);

        $uploadedFile = new UploadedFile($samplePath, 'contoh-data-import-umkm.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($admin)->post(route('admin.umkm.import'), [
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect(route('admin.umkm-profiles.index'));
        $response->assertSessionHas('success');

        // Pastikan minimal salah satu data sampel uji coba tersimpan dengan baik
        $this->assertDatabaseHas('users', [
            'email' => 'agus.bakpiasurya@gmail.com',
            'role'  => 'umkm',
            'phone' => '081223344551',
        ]);

        $this->assertDatabaseHas('umkm_profiles', [
            'business_name'  => 'Bakpia Surya Mentari',
            'owner_name'     => 'H. Agus Mulyadi',
            'kabupaten_kota' => 'Kota Yogyakarta',
        ]);
    }
}
