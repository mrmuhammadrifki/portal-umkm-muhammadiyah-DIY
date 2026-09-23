<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subsector;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAndUmkmSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::firstOrCreate(
            ['email' => 'admin@umkm-muhammadiyah-diy.test'],
            [
                'name' => 'Admin LP UMKM PWM DIY',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081227001912',
                'is_active' => true,
            ]
        );

        // 2. Daftar Data UMKM Beragam (Semua Kabupaten/Kota di DIY & Variasi Status)
        $umkmData = [
            // --- A. APPROVED & AKTIF (Tampil di Katalog Publik & Statistik Beranda) ---
            [
                'user_name'          => 'H. Ahmad Dahlan Saputra',
                'email'              => 'bakpia.menoreh@umkm.test',
                'phone'              => '081234567801',
                'is_active'          => true,
                'business_name'      => 'Bakpia Pathok Menoreh',
                'category'           => 'Kuliner',
                'description'        => "Produsen bakpia pathok legendaris binaan Muhammadiyah dengan resep tradisional turun-temurun. Menggunakan kacang hijau pilihan asli petani lokal, mentega kualitas super, dan tanpa bahan pengawet kimia. Tersedia varian rasa basah dan kering dengan kemasan higienis yang cocok untuk oleh-oleh khas Yogyakarta.",
                'address'            => 'Jl. Kauman No. 42, Ngupasan',
                'kecamatan'          => 'Gondomanan',
                'kabupaten_kota'     => 'Kota Yogyakarta',
                'whatsapp'           => '081234567801',
                'instagram'          => '@bakpia_menoreh_jogja',
                'nib'                => '9120001234567',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'bakpia-menoreh.png',
            ],
            [
                'user_name'          => 'Siti Walidah Handayani',
                'email'              => 'batik.surya@umkm.test',
                'phone'              => '081234567802',
                'is_active'          => true,
                'business_name'      => 'Batik Tulis Surya Malioboro',
                'category'           => 'Fashion',
                'description'        => "Sentra kerajinan batik tulis dan cap halus dengan motif khas Surya Muhammadiyah, Parang Rusak, Kawung, dan flora modern. Dikerjakan langsung oleh pengrajin perempuan lokal dengan pewarna alami ramah lingkungan serta kain katun primissima dan sutra berkualitas tinggi.",
                'address'            => 'Jl. Sosrowijayan No. 18, Sosromenduran',
                'kecamatan'          => 'Danurejan',
                'kabupaten_kota'     => 'Kota Yogyakarta',
                'whatsapp'           => '081234567802',
                'instagram'          => '@batiktulis_suryajogja',
                'nib'                => '9120002345678',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'batik-surya.png',
            ],
            [
                'user_name'          => 'Bambang Sudarsono',
                'email'              => 'perak.kotagede@umkm.test',
                'phone'              => '081234567803',
                'is_active'          => true,
                'business_name'      => 'Kotagede Silver & Filigree Art',
                'category'           => 'Kerajinan',
                'description'        => "Bengkel kriya perak asli Kotagede spesialis teknik filigree (anyaman benang perak 925). Memproduksi souvenir eksklusif, cincin custom, miniatur budaya, dan perhiasan etnik berkualitas ekspor.",
                'address'            => 'Jl. Mondorakan No. 78, Prenggan',
                'kecamatan'          => 'Kotagede',
                'kabupaten_kota'     => 'Kota Yogyakarta',
                'whatsapp'           => '081234567803',
                'instagram'          => '@kotagede.silverart',
                'nib'                => '9120003456789',
                'affiliation_status' => 'non_afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'kotagede-silver.png',
            ],
            [
                'user_name'          => 'Tri Wahyudi',
                'email'              => 'kopi.merapi@umkm.test',
                'phone'              => '081234567804',
                'is_active'          => true,
                'business_name'      => 'Kopi Lereng Merapi Kalingga',
                'category'           => 'Pertanian',
                'description'        => "Kelompok tani kopi lereng selatan Gunung Merapi ketinggian 1.100 mdpl. Menghasilkan biji kopi Arabika dan Robusta kualitas specialty dengan proses basah (full wash), madu (honey), dan natural anaerob beraroma khas tanah vulkanik subur.",
                'address'            => 'Dusun Petung, Kepuharjo',
                'kecamatan'          => 'Cangkringan',
                'kabupaten_kota'     => 'Kabupaten Sleman',
                'whatsapp'           => '081234567804',
                'instagram'          => '@kopimerapi.kalingga',
                'nib'                => '9120004567890',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'kopi-merapi.png',
            ],
            [
                'user_name'          => 'Endang Lestari',
                'email'              => 'belut.godean@umkm.test',
                'phone'              => '081234567805',
                'is_active'          => true,
                'business_name'      => 'Godean Belut Crispy Barokah',
                'category'           => 'Kuliner',
                'description'        => "Olahan camilan keripik belut air tawar khas Godean dengan rempah tradisional gurih, renyah, tahan lama tanpa bahan kimia berbahaya. Telah mengantongi sertifikat P-IRT dan sertifikasi Halal MUI.",
                'address'            => 'Jl. Godean KM 9.5, Sidoagung',
                'kecamatan'          => 'Godean',
                'kabupaten_kota'     => 'Kabupaten Sleman',
                'whatsapp'           => '081234567805',
                'instagram'          => '@belutcrispy.barokah',
                'nib'                => '9120005678901',
                'affiliation_status' => 'non_afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'belut-godean.png',
            ],
            [
                'user_name'          => 'Dwi Haryanto',
                'email'              => 'kulit.manding@umkm.test',
                'phone'              => '081234567806',
                'is_active'          => true,
                'business_name'      => 'Manding Leather Craft & Shoes',
                'category'           => 'Kerajinan',
                'description'        => "Pengrajin kulit sapi asli sentra Manding Bantul. Memproduksi tas selempang vintage, jaket motor kulit, dompet bifold jahit tangan, serta sepatu kulit formal dan casual dengan ketahanan tinggi dan garansi jahitan.",
                'address'            => 'Jl. Parangtritis KM 11, Sabdodadi',
                'kecamatan'          => 'Sewon',
                'kabupaten_kota'     => 'Kabupaten Bantul',
                'whatsapp'           => '081234567806',
                'instagram'          => '@manding_leathergoods',
                'nib'                => '9120006789012',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'manding-leather.png',
            ],
            [
                'user_name'          => 'Suradi Wibowo',
                'email'              => 'gerabah.kasongan@umkm.test',
                'phone'              => '081234567807',
                'is_active'          => true,
                'business_name'      => 'Gerabah Kasongan Artisan',
                'category'           => 'Kerajinan',
                'description'        => "Koleksi gerabah dan keramik terakota estetik untuk dekorasi interior modern dan taman. Menyediakan vas bunga terakota, teko tanah liat tradisional, tempat lilin aromaterapi, dan pot tanaman hias artistik.",
                'address'            => 'Jl. Kasongan RT 04, Bangunjiwo',
                'kecamatan'          => 'Kasihan',
                'kabupaten_kota'     => 'Kabupaten Bantul',
                'whatsapp'           => '081234567807',
                'instagram'          => '@kasongan_terracotta',
                'nib'                => '9120007890123',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'gerabah-kasongan.png',
            ],
            [
                'user_name'          => 'Sutrisno',
                'email'              => 'gulasemut.menoreh@umkm.test',
                'phone'              => '081234567808',
                'is_active'          => true,
                'business_name'      => 'Gula Semut Menoreh Organik',
                'category'           => 'Pertanian',
                'description'        => "Koperasi nira kelapa perbukitan Menoreh Kulon Progo. Mengolah nira segar menjadi gula kristal (gula semut) organik dengan indeks glikemik rendah. Tersedia varian original, rempah jahe, kunyit, dan kayu manis.",
                'address'            => 'Hargorejo, Kokap',
                'kecamatan'          => 'Kokap',
                'kabupaten_kota'     => 'Kabupaten Kulon Progo',
                'whatsapp'           => '081234567808',
                'instagram'          => '@gulasemut_menoreh',
                'nib'                => '9120008901234',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'gula-semut.png',
            ],
            [
                'user_name'          => 'Rohmad Santoso',
                'email'              => 'madu.wanagama@umkm.test',
                'phone'              => '081234567809',
                'is_active'          => true,
                'business_name'      => 'Madu Hutan Wanagama',
                'category'           => 'Pertanian',
                'description'        => "Peternak lebah madu hutan Wanagama Gunungkidul. Memanen madu murni lebah Apis Cerana dan Trigona (Klanceng) tanpa proses pemanasan atau penambahan pemanis buatan, kaya akan enzim aktif dan antioksidan alami.",
                'address'            => 'Banaran, Playen',
                'kecamatan'          => 'Playen',
                'kabupaten_kota'     => 'Kabupaten Gunungkidul',
                'whatsapp'           => '081234567809',
                'instagram'          => '@maduhutan_wanagama',
                'nib'                => '9120009012345',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'madu-wanagama.png',
            ],
            [
                'user_name'          => 'Hendra Kurniawan',
                'email'              => 'sablon.berkah@umkm.test',
                'phone'              => '081234567810',
                'is_active'          => true,
                'business_name'      => 'Berkah Kreasi Sablon & Konveksi',
                'category'           => 'Jasa',
                'description'        => "Layanan jasa sablon kaos manual (plastisol, discharge) dan konveksi pembuatan seragam kemeja, kaos organisasi, jaket himpunan, dan totebag kanvas. Melayani pesanan skala satuan hingga ribuan pcs dengan harga kompetitif dan hasil presisi.",
                'address'            => 'Jl. Magelang KM 6, Sinduadi',
                'kecamatan'          => 'Mlati',
                'kabupaten_kota'     => 'Kabupaten Sleman',
                'whatsapp'           => '081234567810',
                'instagram'          => '@sablonberkah_jogja',
                'nib'                => '9120010123456',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'sablon-berkah.png',
            ],

            // --- B. STATUS PENDING (Untuk Mengetes Halaman Persetujuan Admin /admin/persetujuan-umkm) ---
            [
                'user_name'          => 'Yusuf Maulana',
                'email'              => 'jamur.ngaglik@umkm.test',
                'phone'              => '081234567811',
                'is_active'          => true,
                'business_name'      => 'Jamur Crispy Ngaglik Organik',
                'category'           => 'Kuliner',
                'description'        => "Usaha rintisan budidaya jamur tiram putih dan olahan keripik jamur aneka rasa (barbeque, balado, keju). Mendaftar sebagai mitra binaan baru LP UMKM DIY untuk perluasan pasar dan pendampingan legalitas halal.",
                'address'            => 'Jl. Kaliurang KM 12, Sardonoharjo',
                'kecamatan'          => 'Ngaglik',
                'kabupaten_kota'     => 'Kabupaten Sleman',
                'whatsapp'           => '081234567811',
                'instagram'          => '@jamurcrispy_ngaglik',
                'nib'                => '9120011234567',
                'affiliation_status' => 'afiliasi',
                'status'             => 'pending',
                'logo_slug'          => 'jamur-ngaglik.png',
            ],
            [
                'user_name'          => 'Hj. Marni Suryaningsih',
                'email'              => 'gudeg.marni@umkm.test',
                'phone'              => '081234567812',
                'is_active'          => true,
                'business_name'      => 'Gudeg Kendil Bu Hj. Marni',
                'category'           => 'Kuliner',
                'description'        => "Kuliner khas gudeg basah dan kering dengan kendil tanah liat tradisional serta varian gudeg kaleng siap saji yang tahan hingga 1 tahun. Telah dirintis sejak tahun 1998 di sekitar kawasan njeron beteng Keraton.",
                'address'            => 'Jl. Wijilan No. 25, Panembahan',
                'kecamatan'          => 'Kraton',
                'kabupaten_kota'     => 'Kota Yogyakarta',
                'whatsapp'           => '081234567812',
                'instagram'          => '@gudegkendil_bumarni',
                'nib'                => '9120012345678',
                'affiliation_status' => 'afiliasi',
                'status'             => 'pending',
                'logo_slug'          => 'gudeg-marni.png',
            ],

            // --- C. STATUS REJECTED (Untuk Mengetes Filter Status Ditolak di /admin/umkm) ---
            [
                'user_name'          => 'Wawan Setiawan',
                'email'              => 'bambu.cangkringan@umkm.test',
                'phone'              => '081234567813',
                'is_active'          => true,
                'business_name'      => 'Kerajinan Bambu Cangkringan Indah',
                'category'           => 'Kerajinan',
                'description'        => "Penyedia anyaman bambu dan gazebo bambu taman. Pendaftaran ditolak sementara oleh admin karena berkas alamat fisik dan kontak telepon belum dapat diverifikasi.",
                'address'            => 'Dusun Kaliurang Timur, Hargobinangun',
                'kecamatan'          => 'Pakem',
                'kabupaten_kota'     => 'Kabupaten Sleman',
                'whatsapp'           => '081234567813',
                'instagram'          => null,
                'nib'                => null,
                'affiliation_status' => 'non_afiliasi',
                'status'             => 'rejected',
                'logo_slug'          => 'bambu-cangkringan.png',
            ],

            // --- D. STATUS SUSPENDED / NONAKTIF (is_active = false) (Untuk Mengetes Fitur Suspend/Reactivate Admin & Blokir Login) ---
            [
                'user_name'          => 'Rudi Hartono',
                'email'              => 'toko.bermasalah@umkm.test',
                'phone'              => '081234567814',
                'is_active'          => false, // Disuspend oleh admin
                'business_name'      => 'Busana Muslim Amanah Barokah',
                'category'           => 'Fashion',
                'description'        => "Usaha penjualan busana muslim dan jilbab syar'i. Akun ini dinonaktifkan sementara oleh admin LP UMKM PWM DIY karena ada laporan pesanan fiktif.",
                'address'            => 'Jl. Gedongkuning No. 55, Banguntapan',
                'kecamatan'          => 'Banguntapan',
                'kabupaten_kota'     => 'Kabupaten Bantul',
                'whatsapp'           => '081234567814',
                'instagram'          => '@busanamuslim_amanah',
                'nib'                => '9120014567890',
                'affiliation_status' => 'afiliasi',
                'status'             => 'approved',
                'logo_slug'          => 'busana-amanah.png',
            ],
        ];

        foreach ($umkmData as $item) {
            // Buat logo PNG mock jika belum ada
            $logoPath = DummyImageHelper::createLogo(
                $item['logo_slug'],
                $item['business_name'],
                $item['category']
            );

            // Buat User
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['user_name'],
                    'password' => Hash::make('password'),
                    'role' => 'umkm',
                    'phone' => $item['phone'],
                    'is_active' => $item['is_active'],
                ]
            );

            // Buat atau Update UmkmProfile
            // Nilai default monthly_revenue: acak realistis campuran Mikro-Kecil
            $defaultRevenue = array_rand(array_flip([
                4_000_000, 8_000_000, 12_000_000, 18_000_000, 22_000_000, // Mikro
                30_000_000, 55_000_000, 90_000_000, 140_000_000,           // Kecil
            ]));
            $revenue = $item['monthly_revenue'] ?? $defaultRevenue;

            // Tentukan Kategori Skala Usaha (Mikro, Kecil, Menengah)
            $categorySlug = $revenue < 25_000_000 ? 'mikro' : ($revenue <= 208_000_000 ? 'kecil' : 'menengah');
            $category = Category::where('slug', $categorySlug)->first();

            // Tentukan Subsektor EKRAF
            $catName = $item['category'] ?? 'Kuliner';
            $subsectorQuery = match($catName) {
                'Fashion'   => 'Fashion',
                'Kerajinan' => 'Kriya',
                'Pertanian' => 'Kuliner',
                default     => $catName,
            };
            $subsector = Subsector::where('name', 'like', "%{$subsectorQuery}%")->first();

            UmkmProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name'          => $item['business_name'],
                    'owner_name'             => $item['user_name'],
                    'established_year'       => $item['established_year'] ?? rand(2015, 2023),
                    'employee_count'         => $item['employee_count'] ?? rand(2, 15),
                    'monthly_revenue'        => $revenue,
                    'category_id'            => $category?->id,
                    'subsector_id'           => $subsector?->id,
                    'description'            => $item['description'],
                    'address'                => $item['address'],
                    'kelurahan'              => $item['kelurahan'] ?? null,
                    'kecamatan'              => $item['kecamatan'],
                    'kabupaten_kota'         => $item['kabupaten_kota'],
                    'whatsapp'               => preg_replace('/[^0-9]/', '', $item['whatsapp']),
                    'instagram'              => $item['instagram'],
                    'nib'                    => $item['nib'],
                    'has_halal_certificate'  => $item['has_halal_certificate'] ?? (bool) rand(0, 1),
                    'halal_certificate_year' => $item['has_halal_certificate'] ?? true ? ($item['halal_certificate_year'] ?? rand(2019, 2024)) : null,
                    'has_attended_training'  => $item['has_attended_training'] ?? (rand(0, 1) ? 'ya' : 'tidak'),
                    'logo_path'              => $logoPath,
                    'status'                 => $item['status'],
                ]
            );
        }
    }
}
