<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title'             => 'Klinik Halal UMKM PWM DIY 2025',
                'slug'              => 'klinik-halal-umkm-pwm-diy-2025',
                'type'              => 'pelatihan',
                'summary'           => 'Fasilitasi Sertifikasi Halal Self Declare & NIB gratis bagi 150 pelaku UMKM di D.I. Yogyakarta bersama LP UMKM PWM DIY, Maybank Syariah, LP4H, dan ADHC.',
                'description'       => "Assalamu'alaikum Wr Wb\n\nHallo Sobat UMKM DIY 🙌🏻\n\n\"Dengan potensi besar ekosistem halal kita, saya optimis industri halal kita akan berperan penting dalam memacu pencapaian target pertumbuhan ekonomi nasional sebesar 8% pada tahun 2028-2029 mendatang.\" ungkap Kepala BPJPH Ahmad Haikal Hasan\n\nBagi 150 pelaku UMKM di D.I. Yogyakarta yang belum memiliki maupun yang belum lengkap Sertifikasi Halalnya dan masih *berpenghasilan bersih di bawah Rp. 7.500.000,- per bulannya*, LP UMKM PWM DIY bersama Maybank Syariah, LP4H PWM DIY, dan ADHC akan menyelenggarakan KLINIK HALAL UMKM.\n\n🔒 *CATAT TANGGAL PENTINGNYA & PILIH TANGGAL PELATIHANNYA:*\n- 12-19 Januari 2025: Pendaftaran dan Penjaringan UMKM\n- 25 Januari 2025: Pelatihan Sertifikasi Halal Jilid 1\n- 1 Februari 2025: Pelatihan Sertifikasi Halal Jilid 2\n- 2-16 Februari 2025: Proses Input Data\n- Sertifikat Halal Terbit\n\n📍 Tempat Pelatihan:\nAula Gedung PWM D.I. Yogyakarta\nJalan Gedongkuning No. 130 B, Rejowinangun, Kecamatan Kotagede, Kota Yogyakarta, D.I. Yogyakarta 55171\nLink Gmaps: https://maps.app.goo.gl/YFhqvxEVq2EPD6Gb7\n\n📝 Syarat UMKM yang Bisa Ikut Klinik:\n1. Laba bersih kurang dari 7,5jt per bulan\n2. Usaha sudah beroperasi minimal 1 tahun\n3. Produk yang dihasilkan berupa barang, bukan jasa atau usaha restoran, kantin, catering, dan kedai/rumah/warung makan\n4. Jenis produk/kelompok produk yang disertifikasi halal tidak mengandung unsur hewan hasil sembelihan, kecuali berasal dari produsen atau RPH/RPA bersertifikasi halal\n\n🔎 Keuntungan yang didapat UMKM mengikuti kegiatan ini:\n1. Mendapatkan Sertifikat Halal Self Declare\n2. Mendapatkan Nomor Induk Berusaha (NIB)\n3. Bingkisan dan Transport\n4. Pelatihan Gratis\n5. Relasi dan Jaringan Bisnis Baru\n\n✍🏻 Pendaftaran:\n12 Januari 2025 - 19 Januari 2025\n\n📞 Contact Person:\n- Ibu Amalya | 082280126691",
                'date_start'        => Carbon::parse('2025-01-25 08:30:00'),
                'date_end'          => Carbon::parse('2025-02-01 16:00:00'),
                'location'          => 'Aula Gedung PWM D.I. Yogyakarta, Jl. Gedongkuning No. 130 B, Kotagede, Yogyakarta',
                'organizer'         => 'LP UMKM PWM DIY & Maybank Syariah',
                'speaker'           => 'Tim LP4H PWM DIY & ADHC Halal Center',
                'quota'             => 150,
                'cost'              => 'Gratis (Fasilitasi Penuh + Bingkisan & Transport)',
                'registration_url'  => null,
                'whatsapp_contact'  => '082280126691',
                'contact_person'    => 'Ibu Amalya | 082280126691',
                'is_published'      => true,
                'banner_file'       => 'event-klinik-halal.png',
                'banner_date_label' => '25 Jan - 01 Feb (08.30 WIB)',
            ],
            [
                'title'             => 'Workshop Sertifikasi Halal Self-Declare & NIB Mandiri 2026',
                'slug'              => 'workshop-sertifikasi-halal-nib-mandiri-2026',
                'type'              => 'workshop',
                'summary'           => 'Pendampingan langsung penerbitan NIB berbasis OSS-RBA dan pengurusan Sertifikasi Halal Gratis (SEHATI) bagi pelaku usaha kuliner serta olahan pangan binaan LP UMKM Muhammadiyah DIY.',
                'description'       => "Sertifikasi Halal dan kepemilikan NIB merupakan legalitas krusial bagi UMKM untuk memperluas jangkauan pasar dan menumbuhkan rasa percaya konsumen.\n\nDalam workshop ini, peserta akan dibimbing langsung langkah demi langkah:\n1. Pendaftaran dan verifikasi akun OSS-RBA hingga NIB terbit di tempat.\n2. Verifikasi bahan baku dan proses produk halal (PPH).\n3. Input berkas di aplikasi SIHALAL Kemenag RI hingga memperoleh nomor registrasi pendaftaran sertifikasi halal.\n\nSyarat Peserta:\n- Membawa laptop/smartphone dengan paket data aktif.\n- Membawa KTP pemilik dan foto produk yang dijual.\n- Daftar bahan baku dan resep produk.",
                'date_start'        => Carbon::now()->addDays(14)->setTime(8, 30),
                'date_end'          => Carbon::now()->addDays(14)->setTime(15, 30),
                'location'          => 'Aula Gedung PWM DIY Lt. 2, Jl. Gedongkuning 130 Kotagede',
                'organizer'         => 'LP UMKM PWM DIY & Halal Center',
                'speaker'           => 'Tim Pendamping PPH Kemenag & LP3H Muhammadiyah DIY',
                'quota'             => 50,
                'cost'              => 'Gratis (Fasilitasi Penuh)',
                'registration_url'  => 'https://forms.gle/sample-daftar-halal-lpumkm',
                'whatsapp_contact'  => '081227001912',
                'contact_person'    => 'Bapak Hendra | 081227001912',
                'is_published'      => true,
                'banner_file'       => 'event-halal-nib.png',
                'banner_date_label' => 'Sabtu, 14 Hari Lagi (08.30 - 15.30 WIB)',
            ],
            [
                'title'             => 'Pelatihan Pemasaran Digital & Optimasi WhatsApp Business untuk UMKM',
                'slug'              => 'pelatihan-digital-marketing-whatsapp-business',
                'type'              => 'pelatihan',
                'summary'           => 'Strategi praktis mendatangkan pelanggan baru, teknik closing pesan WhatsApp Business yang efektif, dan optimalisasi promosi katalog digital tanpa boncos.',
                'description'       => "Manfaatkan era digital untuk melipatgandakan omset usaha Anda! Pelatihan ini dirancang aplikatif tanpa teori berbelit-belit.\n\nMateri Pokok:\n- Setting profil profesional WhatsApp Business (katalog, quick replies, broadcast etis).\n- Copywriting penawaran yang memikat audiens lokal.\n- Integrasi tautan WhatsApp dengan portal direktori UMKM Muhammadiyah DIY.\n- Strategi konten foto & video pendek untuk menarik pelanggan sekitar.",
                'date_start'        => Carbon::now()->addDays(21)->setTime(9, 0),
                'date_end'          => Carbon::now()->addDays(21)->setTime(15, 0),
                'location'          => 'Ruang Multimedia PWM DIY & Live Zoom (Hybrid)',
                'organizer'         => 'LP UMKM PWM DIY',
                'speaker'           => 'Praktisi Digital Marketing & Tim Pengembang Portal UMKM',
                'quota'             => 80,
                'cost'              => 'Gratis (Bersertifikat)',
                'registration_url'  => 'https://forms.gle/sample-daftar-digital-marketing',
                'whatsapp_contact'  => '081234567800',
                'contact_person'    => 'Tim Media PWM | 081234567800',
                'is_published'      => true,
                'banner_file'       => 'event-digital-marketing.png',
                'banner_date_label' => 'Sabtu, 21 Hari Lagi (09.00 - 15.00 WIB)',
            ],
            [
                'title'             => 'Pendampingan Tata Kelola Keuangan & Pembukuan Kas Usaha Mikro',
                'slug'              => 'pendampingan-tata-kelola-keuangan-usaha-mikro',
                'type'              => 'pendampingan',
                'summary'           => 'Mentoring klinis pemisahan kas pribadi dan kas usaha, pembuatan laporan arus kas sederhana, serta pengenalan aplikasi pencatatan keuangan digital.',
                'description'       => "Masalah umum yang sering dialami pelaku usaha mikro adalah tercampurnya uang dapur dan modal dagang sehingga sulit berkembang.\n\nFasilitas Pendampingan:\n- Template buku kas harian Excel & cetak fisik siap pakai.\n- Mentoring tatap muka 1-on-1 dengan akademisi akuntansi.\n- Panduan penyusunan laporan keuangan untuk pengajuan permodalan Lazismu / perbankan syariah.",
                'date_start'        => Carbon::now()->addDays(28)->setTime(8, 30),
                'date_end'          => Carbon::now()->addDays(28)->setTime(12, 0),
                'location'          => 'Gedung Pimpinan Daerah Muhammadiyah Bantul, Jl. Jend. Sudirman',
                'organizer'         => 'LP UMKM PWM DIY & Majelis Ekonomi PDM Bantul',
                'speaker'           => 'Dosen Akuntansi & Tim Konsultan Keuangan UMKM',
                'quota'             => 40,
                'cost'              => 'Gratis (Prioritas Mitra Binaan)',
                'registration_url'  => 'https://forms.gle/sample-daftar-keuangan-umkm',
                'whatsapp_contact'  => '081227001912',
                'contact_person'    => 'Bapak Hendra | 081227001912',
                'is_published'      => true,
                'banner_file'       => 'event-keuangan-mikro.png',
                'banner_date_label' => 'Sabtu, 28 Hari Lagi (08.30 - 12.00 WIB)',
            ],
            [
                'title'             => 'Pelatihan Fotografi Produk dengan Smartphone untuk Katalog Digital',
                'slug'              => 'pelatihan-fotografi-produk-smartphone-katalog',
                'type'              => 'pelatihan',
                'summary'           => 'Tips dan trik pencahayaan alami, penataan komposisi (styling), serta editing cepat menggunakan smartphone untuk menghasilkan foto produk berstandar komersial.',
                'description'       => "Foto produk yang menarik adalah kunci pertama memikat pembeli di katalog online maupun marketplace!\n\nYang Dipelajari:\n- Pemanfaatan cahaya alami (window light) dan background sederhana.\n- Komposisi foto produk kuliner, fashion, dan kerajinan tangan.\n- Aplikasi editing gratis di Android/iOS untuk mempercantik warna tanpa filter berlebihan.\n- Praktik langsung memotret produk bawaan masing-masing peserta.",
                'date_start'        => Carbon::now()->addDays(35)->setTime(9, 0),
                'date_end'          => Carbon::now()->addDays(35)->setTime(15, 0),
                'location'          => 'Studio Kreatif PDM Sleman, Mlati',
                'organizer'         => 'LP UMKM PWM DIY & Komunitas Fotografi Warga',
                'speaker'           => 'Kreator Konten & Fotografer Produk Komersial Yogyakarta',
                'quota'             => 35,
                'cost'              => 'Gratis',
                'registration_url'  => 'https://forms.gle/sample-daftar-foto-produk',
                'whatsapp_contact'  => '081234567800',
                'contact_person'    => 'Tim Media PWM | 081234567800',
                'is_published'      => true,
                'banner_file'       => 'event-fotografi-produk.png',
                'banner_date_label' => 'Sabtu, 35 Hari Lagi (09.00 - 15.00 WIB)',
            ],
            [
                'title'             => 'Bazar & Temu Bisnis Akbar UMKM Muhammadiyah DIY 2026',
                'slug'              => 'bazar-temu-bisnis-akbar-umkm-muhammadiyah-diy-2026',
                'type'              => 'workshop',
                'summary'           => 'Pameran gelar produk ratusan kader pelaku usaha se-DIY, temu jaringan kemitraan bisnis (B2B), dan seminar inspirasi wirausaha mandiri.',
                'description'       => "Agenda akbar tahunan LP UMKM Pimpinan Wilayah Muhammadiyah DIY menghadirkan:\n- Stand pameran produk Kuliner, Fashion, Kerajinan, Pertanian, dan Jasa.\n- Forum Temu Bisnis (Business Matching) antara produsen dengan ritel modern & koperasi warga.\n- Seminar Nasional 'Membangun Ekosistem Ekonomi Berkeadilan' bersama pimpinan persyarikatan dan tokoh wirausaha sukses.",
                'date_start'        => Carbon::now()->addDays(50)->setTime(8, 0),
                'date_end'          => Carbon::now()->addDays(51)->setTime(21, 0),
                'location'          => 'Halaman & Gedung PWM D.I. Yogyakarta, Jl. Gedongkuning 130',
                'organizer'         => 'Pimpinan Wilayah Muhammadiyah D.I. Yogyakarta',
                'speaker'           => 'Ketua PWM DIY, Pimpinan LP UMKM Pusat, & Pengusaha Nasional',
                'quota'             => 300,
                'cost'              => 'Gratis & Terbuka untuk Umum',
                'registration_url'  => 'https://forms.gle/sample-daftar-expo-lpumkm',
                'whatsapp_contact'  => '081227001912',
                'contact_person'    => 'Panitia PWM DIY | 081227001912',
                'is_published'      => true,
                'banner_file'       => 'event-bazar-temu-bisnis.png',
                'banner_date_label' => 'Sabtu-Ahad, 50 Hari Lagi (08.00 - 21.00 WIB)',
            ],
        ];

        foreach ($events as $item) {
            $bannerPath = DummyImageHelper::createEventBanner(
                $item['banner_file'],
                $item['title'],
                $item['type'],
                $item['banner_date_label'],
                $item['location']
            );

            Event::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title'            => $item['title'],
                    'type'             => $item['type'],
                    'summary'          => $item['summary'],
                    'description'      => $item['description'],
                    'image_path'       => $bannerPath,
                    'date_start'       => $item['date_start'],
                    'date_end'         => $item['date_end'],
                    'location'         => $item['location'],
                    'organizer'        => $item['organizer'],
                    'speaker'          => $item['speaker'],
                    'quota'            => $item['quota'],
                    'cost'             => $item['cost'],
                    'registration_url' => $item['registration_url'],
                    'whatsapp_contact' => $item['whatsapp_contact'],
                    'contact_person'   => $item['contact_person'] ?? $item['whatsapp_contact'],
                    'is_published'     => $item['is_published'],
                ]
            );
        }
    }
}
