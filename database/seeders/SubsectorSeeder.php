<?php

namespace Database\Seeders;

use App\Models\Subsector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubsectorSeeder extends Seeder
{
    public function run(): void
    {
        $subsectors = [
            [
                'name'        => 'Kuliner',
                'icon'        => '🍲',
                'description' => 'Produksi dan penyajian aneka ragam makanan, minuman olahan tradisional dan modern nusantara.',
            ],
            [
                'name'        => 'Kriya (Kerajinan Tangan)',
                'icon'        => '🏺',
                'description' => 'Kerajinan tangan seni bernilai estetika dan fungsional dari kayu, logam, tanah liat, bambu, dan kulit.',
            ],
            [
                'name'        => 'Seni Rupa',
                'icon'        => '🎨',
                'description' => 'Karya visual berupa lukisan, patung, instalasi seni, grafis, dan karya artistik murni maupun terapan.',
            ],
            [
                'name'        => 'Seni Pertunjukkan',
                'icon'        => '🎭',
                'description' => 'Karya panggung langsung seperti tari, teater, musik etnik, dan pertunjukan seni budaya.',
            ],
            [
                'name'        => 'Fashion',
                'icon'        => '👗',
                'description' => 'Kreasi busana, pakaian muslim, batik, tenun, alas kaki, serta aksesoris gaya hidup bernilai tambah.',
            ],
            [
                'name'        => 'Arsitektur',
                'icon'        => '🏛️',
                'description' => 'Layanan perancangan bangunan, pelestarian arsitektur warisan budaya, dan desain lanskap.',
            ],
            [
                'name'        => 'Desain Interior',
                'icon'        => '🛋️',
                'description' => 'Perancangan tata ruang dalam rumah tinggal, ruang komersial, kantor, dan estetika interior.',
            ],
            [
                'name'        => 'Desain Komunikasi Visual (DKV)',
                'icon'        => '📐',
                'description' => 'Pembuatan identitas merek (branding), tipografi, ilustrasi, materi promosi grafis, dan packaging visual.',
            ],
            [
                'name'        => 'Desain Produk',
                'icon'        => '💡',
                'description' => 'Pengembangan bentuk dan fungsionalitas produk konsumen, peralatan rumah tangga, dan perabot fungsional.',
            ],
            [
                'name'        => 'Modifikasi Otomotif',
                'icon'        => '🏎️',
                'description' => 'Kustomisasi bodi, performa, estetika interior/eksterior kendaraan roda dua dan roda empat.',
            ],
            [
                'name'        => 'Aplikasi (Pengembangan Perangkat Lunak)',
                'icon'        => '📱',
                'description' => 'Pembuatan aplikasi mobile, sistem berbasis web, platform digital, dan solusi software terintegrasi.',
            ],
            [
                'name'        => 'Pengembangan Permainan (Game)',
                'icon'        => '🎮',
                'description' => 'Perancangan gameplay, grafis, mekanik, dan pemrograman game edukasi maupun interaktif lintas platform.',
            ],
            [
                'name'        => 'Teknologi Baru',
                'icon'        => '🤖',
                'description' => 'Inovasi kecerdasan buatan (AI), IoT, otomasi cerdas, robotika, dan teknologi berbasis masa depan.',
            ],
            [
                'name'        => 'Konten Digital',
                'icon'        => '🎬',
                'description' => 'Produksi materi konten kreatif untuk media sosial, webtoon, blog, vlog, dan platform streaming.',
            ],
            [
                'name'        => 'Sulih Suara',
                'icon'        => '🎙️',
                'description' => 'Jasa pengisi suara (voice over), narasi audio, dubbing film/animasi, dan produksi sandiwara suara.',
            ],
            [
                'name'        => 'Film, Animasi, dan Video',
                'icon'        => '🎥',
                'description' => 'Produksi film pendek, dokumenter, serial animasi 2D/3D, dan video profil komersial atau edukasi.',
            ],
            [
                'name'        => 'Fotografi',
                'icon'        => '📷',
                'description' => 'Layanan foto produk komersial, dokumentasi acara, foto udara/drone, dan potret profesional.',
            ],
            [
                'name'        => 'Televisi dan Radio',
                'icon'        => '📻',
                'description' => 'Produksi program siaran audio visual, siniar (podcast), radio komunitas, dan siaran berita edukatif.',
            ],
            [
                'name'        => 'Musik',
                'icon'        => '🎵',
                'description' => 'Penciptaan komposisi lagu, aransemen instrumen, produksi rekaman audio, dan pertunjukan musisi lokal.',
            ],
            [
                'name'        => 'Periklanan (Advertising)',
                'icon'        => '📢',
                'description' => 'Kampanye pemasaran digital, media periklanan kreatif luar ruang, promosi agensi, dan media placement.',
            ],
            [
                'name'        => 'Penerbitan',
                'icon'        => '📚',
                'description' => 'Penerbitan buku literasi, modul pembelajaran, majalah, karya sastra, serta publikasi digital e-book.',
            ],
        ];

        foreach ($subsectors as $item) {
            Subsector::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name'        => $item['name'],
                    'icon'        => $item['icon'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}
