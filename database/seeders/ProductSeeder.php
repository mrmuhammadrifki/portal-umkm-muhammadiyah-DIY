<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subsector;
use App\Models\UmkmProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = Category::pluck('id', 'name');

        // Peta produk per profil usaha
        $productsByUmkm = [
            'Bakpia Pathok Menoreh' => [
                [
                    'name'        => 'Bakpia Basah Kacang Hijau Asli Isi 20',
                    'category'    => 'Kuliner',
                    'description' => "Bakpia basah khas Yogyakarta dengan isian kacang hijau kupas pilihan. Tekstur kulit lembut, isian padat manis gurih alami tanpa pemanis buatan. Daya tahan 5-7 hari di suhu ruangan, atau hingga 2 minggu di dalam lemari pendingin. Sangat pas dinikmati bersama teh hangat.",
                    'status'      => 'active',
                    'slug_base'   => 'bakpia-basah-kacang-hijau',
                    'photos'      => [
                        ['label' => 'Kemasan Kotak Isi 20'],
                        ['label' => 'Tekstur Kulit Lembut'],
                        ['label' => 'Isian Kacang Hijau Padat'],
                    ],
                ],
                [
                    'name'        => 'Bakpia Panggang Kering Keju & Cokelat Special',
                    'category'    => 'Kuliner',
                    'description' => "Varian bakpia kering panggang renyah dengan isian keju cheddar kraft dan pasta cokelat leleh berkualitas. Tahan renyah hingga 1 bulan, cocok untuk perjalanan jauh ke luar pulau.",
                    'status'      => 'active',
                    'slug_base'   => 'bakpia-kering-keju-cokelat',
                    'photos'      => [
                        ['label' => 'Tampilan Luar Panggang Renyah'],
                        ['label' => 'Potongan Isian Keju & Cokelat'],
                    ],
                ],
                [
                    'name'        => 'Pia Durian Khas Yogyakarta (Varian Musiman)',
                    'category'    => 'Kuliner',
                    'description' => "Varian premium dengan daging buah durian montong asli. Dibuat terbatas saat musim panen durian tiba.",
                    'status'      => 'inactive', // Inactive untuk pengujian status
                    'slug_base'   => 'pia-durian-musiman',
                    'photos'      => [
                        ['label' => 'Varian Durian Musiman'],
                    ],
                ],
            ],

            'Batik Tulis Surya Malioboro' => [
                [
                    'name'        => 'Kemeja Batik Tulis Pria Motif Parang Surya',
                    'category'    => 'Fashion',
                    'description' => "Kemeja batik tulis sutra ATBM eksklusif dengan paduan motif Parang Barong dan lambang Surya Muhammadiyah. Dikerjakan tangan secara teliti selama 2 bulan oleh pengrajin senior. Dilapisi furing trikot sutra adem, potongan slim-fit dan regular modern.",
                    'status'      => 'active',
                    'slug_base'   => 'kemeja-batik-parang-surya',
                    'photos'      => [
                        ['label' => 'Tampak Depan Kemeja'],
                        ['label' => 'Detail Canting Tulis Halus'],
                        ['label' => 'Kerah & Kancing Tersembunyi'],
                    ],
                ],
                [
                    'name'        => 'Kain Panjang Batik Tulis Pewarna Alam Mangrove',
                    'category'    => 'Fashion',
                    'description' => "Kain jarik batik tulis panjang ukuran 2,5m x 1,15m. Menggunakan pewarna alami dari ekstrak kulit kayu mangrove dan tingi yang menghasilkan warna cokelat soga khas Keraton Yogyakarta.",
                    'status'      => 'active',
                    'slug_base'   => 'kain-batik-pewarna-alam',
                    'photos'      => [
                        ['label' => 'Bentangan Penuh Kain Batik'],
                        ['label' => 'Tekstur Katun Primissima Super'],
                    ],
                ],
                [
                    'name'        => 'Outer Batik Cap Etnik Kombinasi Tenun Lurik',
                    'category'    => 'Fashion',
                    'description' => "Outer casual wanita kombinasi batik cap kontemporer dan tenun lurik Klaten. Cocok untuk busana kantor santai maupun acara semi-formal.",
                    'status'      => 'active',
                    'slug_base'   => 'outer-batik-cap-etnik',
                    'photos'      => [
                        ['label' => 'Tampak Depan Model Outer'],
                        ['label' => 'Kombinasi Tenun Lurik'],
                    ],
                ],
            ],

            'Kotagede Silver & Filigree Art' => [
                [
                    'name'        => 'Miniatur Candi Borobudur Filigree Perak 925',
                    'category'    => 'Kerajinan',
                    'description' => "Karya kriya adiluhung perak bakar 925 berbentuk miniatur Candi Borobudur dengan teknik filigree (anyaman benang perak). Dilengkapi tatakan kayu jati berukir dan kotak mika akrilik kedap udara. Pilihan cenderamata terbaik untuk tamu kenegaraan dan korporasi.",
                    'status'      => 'active',
                    'slug_base'   => 'miniatur-borobudur-filigree',
                    'photos'      => [
                        ['label' => 'Perspektif Candi Borobudur'],
                        ['label' => 'Detail Anyaman Stupa Perak'],
                        ['label' => 'Kotak Akrilik & Dudukan Kayu Jati'],
                    ],
                ],
                [
                    'name'        => 'Cincin Perak Ukir Tradisional Motif Floral Jawa',
                    'category'    => 'Kerajinan',
                    'description' => "Cincin perak sterling 925 handmade dengan ukiran motif sulur daun Jawa. Dilapisi rhodium anti kusam dan aman untuk kulit sensitif. Bisa custom ukuran jari.",
                    'status'      => 'active',
                    'slug_base'   => 'cincin-perak-ukir-jawa',
                    'photos'      => [
                        ['label' => 'Tampak Atas Cincin Ukir'],
                        ['label' => 'Detail Garis Grafir Halus'],
                    ],
                ],
            ],

            'Kopi Lereng Merapi Kalingga' => [
                [
                    'name'        => 'Kopi Arabika Merapi Honey Process Kemasan 200g',
                    'category'    => 'Pertanian',
                    'description' => "Kopi Arabika single origin dari kebun lereng selatan Gunung Merapi ketinggian 1.200 mdpl. Diproses dengan metode Honey (semi-wash) menghasilkan cita rasa manis alami gula tebu, keasaman buah jeruk segar (citrus), dan aroma rempah floral.",
                    'status'      => 'active',
                    'slug_base'   => 'kopi-arabika-merapi-honey',
                    'photos'      => [
                        ['label' => 'Kemasan Pouch Zipper Valve'],
                        ['label' => 'Biji Sangrai Medium Roast'],
                        ['label' => 'Hasil Seduhan Cangkir V60'],
                    ],
                ],
                [
                    'name'        => 'Kopi Robusta Merapi Petik Merah Bubuk 250g',
                    'category'    => 'Pertanian',
                    'description' => "Kopi Robusta murni 100% dari buah petik merah matang pohon. Memiliki body tebal (bold), aroma dark chocolate, dan tingkat kepahitan seimbang tanpa rasa langu. Cocok untuk kopi susu kekinian atau seduh tubruk tradisional.",
                    'status'      => 'active',
                    'slug_base'   => 'kopi-robusta-merapi-petik-merah',
                    'photos'      => [
                        ['label' => 'Kemasan Foil 250g'],
                        ['label' => 'Bubuk Halus Seduh Tubruk'],
                    ],
                ],
                [
                    'name'        => 'Cascara Tea (Teh Kulit Ceri Kopi Merapi Alami)',
                    'category'    => 'Kuliner',
                    'description' => "Seduhan unik dari kulit buah ceri kopi arabika yang dikeringkan di bawah sinar matahari. Kaya antioksidan, beraroma buah kismis dan asam manis alami.",
                    'status'      => 'active',
                    'slug_base'   => 'cascara-tea-merapi',
                    'photos'      => [
                        ['label' => 'Kemasan Tin Can Cascara Tea'],
                    ],
                ],
            ],

            'Godean Belut Crispy Barokah' => [
                [
                    'name'        => 'Keripik Belut Gurih Renyah Khas Godean 250g',
                    'category'    => 'Kuliner',
                    'description' => "Camilan khas legendaris Godean Sleman berbahan dasar belut sawah segar yang dibersihkan higienis, dibumbui ketumbar dan bawang putih lokal, lalu digoreng dengan tepung beras renyah sampai kriuk tahan lama.",
                    'status'      => 'active',
                    'slug_base'   => 'keripik-belut-godean',
                    'photos'      => [
                        ['label' => 'Kemasan Standing Pouch 250g'],
                        ['label' => 'Kriuk Belut Renyah Gurih'],
                    ],
                ],
                [
                    'name'        => 'Sambal Ulek Belut Asap Pedas Nendang 150g',
                    'category'    => 'Kuliner',
                    'description' => "Perpaduan suwiran belut asap gurih dengan ulekan cabai rawit merah pedas mantap. Siap santap langsung bersama nasi hangat.",
                    'status'      => 'active',
                    'slug_base'   => 'sambal-ulek-belut-asap',
                    'photos'      => [
                        ['label' => 'Toples Segel Sambal Belut'],
                        ['label' => 'Sajian Bersama Nasi Hangat'],
                    ],
                ],
            ],

            'Manding Leather Craft & Shoes' => [
                [
                    'name'        => 'Tas Selempang Pria Kulit Sapi Crazy Horse Vintage',
                    'category'    => 'Kerajinan',
                    'description' => "Tas kerja pria model messenger bag dari kulit sapi asli jenis Crazy Horse. Menampilkan efek goresan vintage artistik yang semakin estetik seiring pemakaian waktu. Memuat laptop hingga 14 inch, dokumen A4, dan kompartemen gadget khusus.",
                    'status'      => 'active',
                    'slug_base'   => 'tas-selempang-kulit-crazy-horse',
                    'photos'      => [
                        ['label' => 'Tampak Depan Tas Messenger'],
                        ['label' => 'Kompartemen Laptop & Gadget'],
                        ['label' => 'Buckle Kuningan Antik'],
                    ],
                ],
                [
                    'name'        => 'Sepatu Boots Pria Kulit Asli Pull Up Tan',
                    'category'    => 'Fashion',
                    'description' => "Sepatu boots kasual pria berbahan kulit sapi pull up warna cokelat tan. Dilengkapi sol karet anti licin dengan konstruksi jahitan sol melingkar kuat (Goodyear welted style) yang awet bertahun-tahun.",
                    'status'      => 'active',
                    'slug_base'   => 'sepatu-boots-kulit-pull-up',
                    'photos'      => [
                        ['label' => 'Tampak Samping Sepatu Boots'],
                        ['label' => 'Sol Karet Jahitan Melingkar'],
                        ['label' => 'Kemasan Box Kayu Eksklusif'],
                    ],
                ],
                [
                    'name'        => 'Dompet Bifold Kulit Nabati Jahit Tangan',
                    'category'    => 'Kerajinan',
                    'description' => "Dompet pria lipat dua dari bahan kulit samak nabati (veg-tan). Dijahit tangan menggunakan benang lilin poliester tebal dengan 6 slot kartu dan 2 kompartemen uang kertas.",
                    'status'      => 'active',
                    'slug_base'   => 'dompet-kulit-nabati-bifold',
                    'photos'      => [
                        ['label' => 'Tampak Luar Dompet Kulit'],
                        ['label' => 'Slot Kartu & Jahitan Tangan'],
                    ],
                ],
            ],

            'Gerabah Kasongan Artisan' => [
                [
                    'name'        => 'Vas Bunga Terakota Estetik Etnik Skandinavia',
                    'category'    => 'Kerajinan',
                    'description' => "Vas bunga dekorasi interior berbahan tanah liat bakar bersuhu tinggi khas Kasongan Bantul. Mengusung bentuk minimalis bernuansa Skandinavia dengan finishing matte alami yang hangat untuk ruang tamu dan kafe.",
                    'status'      => 'active',
                    'slug_base'   => 'vas-bunga-terakota-kasongan',
                    'photos'      => [
                        ['label' => 'Vas Bunga Terakota Minimalis'],
                        ['label' => 'Detail Tekstur Tanah Liat Alami'],
                        ['label' => 'Display dengan Bunga Kering'],
                    ],
                ],
                [
                    'name'        => 'Set Teko & Cangkir Tanah Liat Tradisional Jawa',
                    'category'    => 'Kerajinan',
                    'description' => "Satu set perlengkapan minum teh poci tradisional berisi 1 teko dengan saringan alami dan 4 cangkir gerabah. Memberikan aroma tanah liat khas saat menyeduh teh melati tubruk gula batu.",
                    'status'      => 'active',
                    'slug_base'   => 'set-teko-gerabah-tradisional',
                    'photos'      => [
                        ['label' => 'Set Lengkap 1 Teko 4 Cangkir'],
                        ['label' => 'Gagang Kayu Ergonomis'],
                    ],
                ],
            ],

            'Gula Semut Menoreh Organik' => [
                [
                    'name'        => 'Gula Semut Kelapa Organik Asli Menoreh 500g',
                    'category'    => 'Pertanian',
                    'description' => "Gula kristal nira kelapa organik murni tanpa campuran sulfit atau pengawet kimiawi. Memiliki aroma karamel wangi alami dan indeks glikemik rendah (GI ~35-40), pilihan pemanis sehat untuk kopi, teh, dan aneka kue kering.",
                    'status'      => 'active',
                    'slug_base'   => 'gula-semut-kelapa-500g',
                    'photos'      => [
                        ['label' => 'Kemasan Standing Pouch Eco'],
                        ['label' => 'Bulir Gula Kristal Halus Kering'],
                    ],
                ],
                [
                    'name'        => 'Gula Semut Jahe Merah Seduh Hangat 250g',
                    'category'    => 'Kuliner',
                    'description' => "Paduan nira kelapa kristal dengan ekstrak jahe merah murni dan rempah cengkeh sereh. Cukup seduh 2 sendok makan dengan air panas untuk wedang jahe penghangat tubuh alami.",
                    'status'      => 'active',
                    'slug_base'   => 'gula-semut-jahe-merah',
                    'photos'      => [
                        ['label' => 'Kemasan Praktis 250g'],
                        ['label' => 'Seduhan Wedang Jahe Hangat'],
                    ],
                ],
            ],

            'Madu Hutan Wanagama' => [
                [
                    'name'        => 'Madu Murni Nektar Akasia Mangium Wanagama 500ml',
                    'category'    => 'Pertanian',
                    'description' => "Madu lebah liar hutan Wanagama Gunungkidul dari pohon Akasia Mangium. Dipanen dengan metode lestari tanpa merusak sarang lebah. Kadar air rendah alami (18-20%), tekstur kental, rasa manis legit berkhasiat meningkatkan daya tahan tubuh.",
                    'status'      => 'active',
                    'slug_base'   => 'madu-nektar-akasia-wanagama',
                    'photos'      => [
                        ['label' => 'Botol Kaca Hexagonal 500ml'],
                        ['label' => 'Uji Kekentalan Sendok Kayu'],
                    ],
                ],
                [
                    'name'        => 'Madu Klanceng Liar Propolis Trigona Asli 250ml',
                    'category'    => 'Pertanian',
                    'description' => "Madu lebah tanpa sengat (Trigona / Klanceng) berkhasiat tinggi dengan kandungan propolis dan asam fenolat alami. Memiliki rasa manis asam segar khas yang ampuh meredakan radang tenggorokan dan batuk.",
                    'status'      => 'active',
                    'slug_base'   => 'madu-klanceng-trigona-asli',
                    'photos'      => [
                        ['label' => 'Kemasan Botol Segel 250ml'],
                        ['label' => 'Warna Cokelat Khas Klanceng'],
                        ['label' => 'Hasil Uji Lab Kemurnian'],
                    ],
                ],
            ],

            'Berkah Kreasi Sablon & Konveksi' => [
                [
                    'name'        => 'Jasa Sablon Kaos Plastisol High Density Premium',
                    'category'    => 'Jasa',
                    'description' => "Layanan jasa sablon kaos manual dengan tinta plastisol impor curing suhu tinggi. Hasil sablonan lentur, warna pekat tidak retak, dan tahan cuci mesin. Melayani pesanan kaos komunitas, organisasi otonom Muhammadiyah, dan gathering perusahaan.",
                    'status'      => 'active',
                    'slug_base'   => 'jasa-sablon-kaos-plastisol',
                    'photos'      => [
                        ['label' => 'Hasil Sablon Timbul Presisi'],
                        ['label' => 'Koleksi Warna Tinta Plastisol'],
                    ],
                ],
                [
                    'name'        => 'Jasa Pembuatan Kemeja Korsa / PDH Drill Organisasi',
                    'category'    => 'Jasa',
                    'description' => "Konveksi kemeja lapangan, PDH, dan korsa dengan bahan Japan Drill atau American Drill tebal dan adem. Dilengkapi bordir komputer detail presisi tinggi untuk logo dan nama personal.",
                    'status'      => 'active',
                    'slug_base'   => 'jasa-kemeja-korsa-drill',
                    'photos'      => [
                        ['label' => 'Contoh Kemeja Korsa Jadi'],
                        ['label' => 'Detail Bordir Komputer'],
                    ],
                ],
            ],

            // Profil Pending (untuk pengujian admin)
            'Jamur Crispy Ngaglik Organik' => [
                [
                    'name'        => 'Keripik Jamur Tiram Crispy Aneka Rasa 100g',
                    'category'    => 'Kuliner',
                    'description' => "Camilan keripik jamur tiram putih organik dengan bumbu tabur BBQ dan Balado tanpa MSG.",
                    'status'      => 'active',
                    'slug_base'   => 'keripik-jamur-tiram-aneka-rasa',
                    'photos'      => [
                        ['label' => 'Kemasan Pouch Jamur Crispy'],
                    ],
                ],
            ],

            'Gudeg Kendil Bu Hj. Marni' => [
                [
                    'name'        => 'Paket Gudeg Kendil Komplit Ayam Kampung & Telur',
                    'category'    => 'Kuliner',
                    'description' => "Gudeg nangka muda kendil tanah liat khas Wijilan komplit dengan ayam kampung suwir, telur bebek pindang, tahu krecek pedas, dan areh gurih.",
                    'status'      => 'active',
                    'slug_base'   => 'paket-gudeg-kendil-komplit',
                    'photos'      => [
                        ['label' => 'Kendil Tradisional Tanah Liat'],
                        ['label' => 'Sajian Piring Komplit'],
                    ],
                ],
            ],

            // Profil Suspended (untuk pengujian admin)
            'Busana Muslim Amanah Barokah' => [
                [
                    'name'        => 'Gamis Syari Elegan Katun Madinah',
                    'category'    => 'Fashion',
                    'description' => "Gamis syari polos wanita muslimah dengan bahan katun madinah premium yang lembut dan jatuh.",
                    'status'      => 'active',
                    'slug_base'   => 'gamis-syari-katun-madinah',
                    'photos'      => [
                        ['label' => 'Tampak Depan Gamis'],
                        ['label' => 'Khimar & Manset Lengan'],
                    ],
                ],
            ],
        ];

        foreach ($productsByUmkm as $businessName => $products) {
            $umkm = UmkmProfile::where('business_name', $businessName)->first();
            if (!$umkm) {
                continue;
            }

            foreach ($products as $prodData) {
                $subsectorName = match($prodData['category']) {
                    'Fashion'   => 'Fashion',
                    'Kerajinan' => 'Kriya',
                    'Pertanian' => 'Kuliner',
                    default     => $prodData['category'],
                };
                $subsector = Subsector::where('name', 'like', "%{$subsectorName}%")->first();
                $totalPhotos = count($prodData['photos']);

                // Generate foto pertama sebagai foto sampul (cover image / legacy image_path)
                $firstPhotoFile = "{$prodData['slug_base']}-1.png";
                $coverImagePath = DummyImageHelper::createProductImage(
                    $firstPhotoFile,
                    $prodData['name'],
                    $prodData['category'],
                    1,
                    $totalPhotos,
                    $prodData['photos'][0]['label'] ?? 'Tampak Utama'
                );

                // Buat atau Update Produk
                $product = Product::updateOrCreate(
                    [
                        'umkm_id' => $umkm->id,
                        'name'    => $prodData['name'],
                    ],
                    [
                        'subsector_id' => $subsector?->id,
                        'category_id'  => $umkm->category_id,
                        'description'  => $prodData['description'],
                        'image_path'   => $coverImagePath,
                        'status'       => $prodData['status'],
                    ]
                );

                // Bersihkan relasi foto lama jika ada
                $product->images()->delete();

                // Generate seluruh foto dalam galeri (ProductImage)
                foreach ($prodData['photos'] as $idx => $photoInfo) {
                    $photoNum = $idx + 1;
                    $photoFile = "{$prodData['slug_base']}-{$photoNum}.png";

                    $imagePath = ($photoNum === 1)
                        ? $coverImagePath
                        : DummyImageHelper::createProductImage(
                            $photoFile,
                            $prodData['name'],
                            $prodData['category'],
                            $photoNum,
                            $totalPhotos,
                            $photoInfo['label'] ?? "Foto ke-{$photoNum}"
                        );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'sort_order' => $idx,
                    ]);
                }
            }
        }
    }
}
