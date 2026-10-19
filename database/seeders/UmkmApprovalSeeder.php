<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subsector;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh untuk halaman /admin/persetujuan-umkm: antrean pending
 * tambahan dan histori keputusan admin (disetujui + ditolak beserta alasan).
 * Aman dijalankan ulang (updateOrCreate berdasarkan email).
 */
class UmkmApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $this->command?->warn('Admin belum ada — jalankan UserAndUmkmSeeder terlebih dahulu.');

            return;
        }

        $rows = [
            // --- Antrean menunggu persetujuan ---
            ['Dewi Lestari', 'keripik.pakem@umkm.test', '081234567821', 'Keripik Singkong Pakem Renyah', 'Kuliner', 'Kabupaten Sleman', 'Pakem', 'Produsen keripik singkong aneka rasa dari singkong petani lokal lereng Merapi.', 'pending', null, null],
            ['Agus Prasetyo', 'tenun.lurik@umkm.test', '081234567822', 'Lurik Klaten Mandiri Jogja', 'Fashion', 'Kabupaten Bantul', 'Sewon', 'Kain dan busana lurik tenun ATBM dengan motif modern untuk pasar anak muda.', 'pending', null, null],
            ['Nur Hidayah', 'es.dawet@umkm.test', '081234567823', 'Dawet Ayu Sentolo', 'Kuliner', 'Kabupaten Kulon Progo', 'Sentolo', 'Minuman tradisional dawet dengan gula aren asli, melayani katering acara pengajian.', 'pending', null, null],

            // --- Histori: disetujui ---
            ['Fitri Handayani', 'tahu.bacem@umkm.test', '081234567824', 'Tahu Bacem Bu Fitri', 'Kuliner', 'Kota Yogyakarta', 'Umbulharjo', 'Olahan tahu bacem dan tempe bacem kemasan vakum tahan 3 hari.', 'approved', null, 2],
            ['Slamet Riyadi', 'anyaman.mendong@umkm.test', '081234567825', 'Anyaman Mendong Sedayu', 'Kerajinan', 'Kabupaten Bantul', 'Sedayu', 'Tikar, tas, dan hiasan dari serat mendong hasil karya kelompok ibu-ibu binaan.', 'approved', null, 6],
            ['Lutfi Ramadhan', 'kopi.gunungkidul@umkm.test', '081234567826', 'Kopi Wonosari Sangrai', 'Kuliner', 'Kabupaten Gunungkidul', 'Wonosari', 'Roastery rumahan biji kopi robusta dan arabika dari kebun rakyat Gunungkidul.', 'approved', null, 11],

            // --- Histori: ditolak beserta alasan ---
            ['Joko Susilo', 'sablon.fiktif@umkm.test', '081234567827', 'Konveksi Cepat Jaya', 'Fashion', 'Kabupaten Sleman', 'Depok', 'Konveksi kaos dan seragam.', 'rejected', 'Nomor WhatsApp tidak aktif dan alamat usaha tidak dapat diverifikasi. Mohon lengkapi alamat lengkap dan kontak yang bisa dihubungi.', 4],
            ['Rina Marlina', 'kosmetik.herbal@umkm.test', '081234567828', 'Herbal Cantik Alami', 'Lainnya', 'Kota Yogyakarta', 'Tegalrejo', 'Produk kosmetik herbal.', 'rejected', 'Deskripsi usaha terlalu singkat dan belum mencantumkan izin edar BPOM untuk produk kosmetik. Mohon lengkapi sebelum mengajukan kembali.', 8],
            ['Hendra Wijaya', 'duplikat.bakpia@umkm.test', '081234567829', 'Bakpia Pathok Menoreh 2', 'Kuliner', 'Kota Yogyakarta', 'Gondomanan', 'Bakpia pathok kacang hijau.', 'rejected', 'Data usaha terindikasi duplikat dengan "Bakpia Pathok Menoreh" yang sudah terdaftar. Gunakan akun yang sudah ada.', 15],
        ];

        foreach ($rows as [$owner, $email, $phone, $business, $cat, $city, $district, $desc, $status, $reason, $daysAgo]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'      => $owner,
                    'password'  => Hash::make('password'),
                    'role'      => 'umkm',
                    'phone'     => $phone,
                    'is_active' => true,
                ]
            );

            $subsectorKeyword = match ($cat) {
                'Kerajinan' => 'Kriya',
                default     => $cat,
            };

            UmkmProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name'          => $business,
                    'owner_name'             => $owner,
                    'description'            => $desc,
                    'address'                => 'Kec. ' . $district . ', ' . $city,
                    'kecamatan'              => $district,
                    'kabupaten_kota'         => $city,
                    'whatsapp'               => $phone,
                    'subsector_id'           => Subsector::where('name', 'like', "%{$subsectorKeyword}%")->value('id'),
                    'category_id'            => Category::where('slug', 'mikro')->value('id'),
                    'monthly_revenue'        => 10_000_000,
                    'has_halal_certificate'  => false,
                    'has_attended_training'  => 'tidak',
                    'status'                 => $status,
                    'rejection_reason'       => $reason,
                    'reviewed_at'            => $daysAgo ? now()->subDays($daysAgo)->setTime(rand(8, 16), rand(0, 59)) : null,
                    'reviewed_by'            => $daysAgo ? $admin->id : null,
                ]
            );
        }

        // UMKM penolakan dari UserAndUmkmSeeder ikut diberi alasan + jejak peninjauan
        UmkmProfile::whereHas('user', fn ($q) => $q->where('email', 'bambu.cangkringan@umkm.test'))
            ->update([
                'rejection_reason' => 'Alamat fisik dan nomor telepon belum dapat diverifikasi. Mohon cantumkan alamat lengkap beserta titik patokan lokasi.',
                'reviewed_at'      => now()->subDays(1)->setTime(10, 30),
                'reviewed_by'      => $admin->id,
            ]);
    }
}
