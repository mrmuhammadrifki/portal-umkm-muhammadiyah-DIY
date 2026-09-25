<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Subsector;

$spreadsheet = new Spreadsheet();

// -------------------------------------------------------------
// SHEET 1: DATA UJI COBA UMKM
// -------------------------------------------------------------
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data UMKM Uji Coba');

$headers = [
    'A1' => 'Nama Usaha *',
    'B1' => 'Nama Pemilik *',
    'C1' => 'Email Akun *',
    'D1' => 'Password Akun',
    'E1' => 'Nomor WhatsApp *',
    'F1' => 'Subsektor EKRAF',
    'G1' => 'Kabupaten / Kota',
    'H1' => 'Kecamatan',
    'I1' => 'Kelurahan / Desa',
    'J1' => 'Alamat Lengkap',
    'K1' => 'Tahun Berdiri',
    'L1' => 'Jumlah Karyawan',
    'M1' => 'Omzet Bulanan (Rp)',
    'N1' => 'NIB',
    'O1' => 'Sertifikat Halal (Ya/Tidak)',
    'P1' => 'Tahun Sertifikat Halal',
    'Q1' => 'Pernah Pelatihan (Ya/Tidak)',
    'R1' => 'Instagram',
    'S1' => 'Deskripsi Usaha',
    'T1' => 'Status Moderasi',
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Header styling: Green Muhammadiyah (#15803D), White bold text
$headerRange = 'A1:T1';
$sheet->getStyle($headerRange)->applyFromArray([
    'font' => [
        'bold'  => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size'  => 11,
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '15803D'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => '0D5226'],
        ],
    ],
]);
$sheet->getRowDimension(1)->setRowHeight(32);

// 10 Data UMKM Uji Coba Nyata
$sampleData = [
    [
        'Bakpia Surya Mentari',
        'H. Agus Mulyadi',
        'agus.bakpiasurya@gmail.com',
        'password123',
        '081223344551',
        'Kuliner',
        'Kota Yogyakarta',
        'Gondomanan',
        'Prawirodirjan',
        'Jl. Brigjen Katamso No. 88',
        '2017',
        '6',
        '28000000',
        '9120005544331',
        'Ya',
        '2021',
        'Ya',
        '@bakpiasurya_yk',
        'Spesialis bakpia basah dan bakpia kukus aneka rasa khas warga Muhammadiyah Gondomanan.',
        'Approved',
    ],
    [
        'Batik Canting Walidah',
        'Hj. Siti Nurjanah',
        'siti.cantingwalidah@gmail.com',
        'password123',
        '081398765402',
        'Fashion',
        'Kabupaten Sleman',
        'Mlati',
        'Sinduadi',
        'Jl. Magelang KM 6.5 No. 12',
        '2019',
        '10',
        '45000000',
        '9120006655442',
        'Tidak',
        '',
        'Ya',
        '@cantingwalidah.batik',
        'Pemberdayaan Aisyiyah dalam pembuatan busana muslim dan kain batik motif kontemporer.',
        'Approved',
    ],
    [
        'Kerajinan Kulit Surya Manding',
        'Rahmat Hidayat',
        'rahmat.kulitmanding@gmail.com',
        'password123',
        '081567891203',
        'Kriya (Kerajinan Tangan)',
        'Kabupaten Bantul',
        'Sewon',
        'Panggungharjo',
        'Sentra Kulit Manding Jl. Parangtritis KM 11',
        '2015',
        '8',
        '52000000',
        '9120007766553',
        'Tidak',
        '',
        'Ya',
        '@manding_suryaleather',
        'Kerajinan dompet, tas, ikat pinggang, dan jaket kulit sapi asli kualitas ekspor.',
        'Approved',
    ],
    [
        'Kopi Lereng Merapi Barokah',
        'Eko Prasetyo',
        'eko.kopimerapi@gmail.com',
        'password123',
        '081789012304',
        'Kuliner',
        'Kabupaten Sleman',
        'Cangkringan',
        'Umbulharjo',
        'Dusun Petung RT 02 RW 05, Cangkringan',
        '2020',
        '4',
        '18500000',
        '9120008877664',
        'Ya',
        '2023',
        'Tidak',
        '@kopimerapi_barokah',
        'Biji kopi Robusta dan Arabika petik merah dari lereng Gunung Merapi diproses roasted bean dan bubuk.',
        'Approved',
    ],
    [
        'Gula Semut Nira Menoreh',
        'Tri Wahyuni',
        'wahyuni.gulasemut@gmail.com',
        'password123',
        '081890123405',
        'Kuliner',
        'Kabupaten Kulon Progo',
        'Kokap',
        'Hargorejo',
        'Jl. Sermo - Kokap KM 3',
        '2018',
        '12',
        '38000000',
        '9120009988775',
        'Ya',
        '2022',
        'Ya',
        '@niramenoreh_organik',
        'Produksi gula kelapa kristal organik (gula semut) aneka varian rasa rempah jahe, kunyit, dan temulawak.',
        'Approved',
    ],
    [
        'Madu Hutan Wanagama Al-Kautsar',
        'Arif Rahman Hakim',
        'arif.maduwanagama@gmail.com',
        'password123',
        '081901234506',
        'Kuliner',
        'Kabupaten Gunungkidul',
        'Playen',
        'Banaran',
        'Kawasan Hutan Wanagama, Playen',
        '2021',
        '3',
        '12000000',
        '9120011122336',
        'Ya',
        '2023',
        'Tidak',
        '@madukautsar_gunungkidul',
        'Madu murni nektar multiflora hasil budidaya lebah Apis Cerana dan Klanceng hutan Wanagama.',
        'Approved',
    ],
    [
        'Studio Desain Kreatif Mentari',
        'Fajar Anindito',
        'fajar.mentaristudio@gmail.com',
        'password123',
        '082134567807',
        'Desain Komunikasi Visual (DKV)',
        'Kota Yogyakarta',
        'Umbulharjo',
        'Semaki',
        'Jl. Kusumanegara No. 115',
        '2022',
        '5',
        '22000000',
        '9120012233447',
        'Tidak',
        '',
        'Ya',
        '@mentaristudio.id',
        'Jasa branding UMKM, desain kemasan, foto produk katalog, dan optimasi visual media sosial.',
        'Approved',
    ],
    [
        'Penerbitan & Percetakan Suara Surya',
        'Dr. H. Muhsin Ash-Shiddiq',
        'muhsin.suarasurya@gmail.com',
        'password123',
        '082245678908',
        'Penerbitan',
        'Kabupaten Sleman',
        'Depok',
        'Caturtunggal',
        'Jl. Gejayan No. 23B',
        '2016',
        '15',
        '215000000',
        '9120013344558',
        'Tidak',
        '',
        'Ya',
        '@suarasurya_press',
        'Penerbitan buku ajar, modul dakwah, literatur keislaman, serta percetakan offset kemasan produk.',
        'Approved',
    ],
    [
        'Gerabah Seni Kasongan Barokah',
        'Sukirno',
        'sukirno.kasongan@gmail.com',
        'password123',
        '082356789009',
        'Kriya (Kerajinan Tangan)',
        'Kabupaten Bantul',
        'Kasihan',
        'Bangunjiwo',
        'Dusun Kajen RT 04, Kasongan',
        '2014',
        '7',
        '32000000',
        '9120014455669',
        'Tidak',
        '',
        'Tidak',
        '@kasongan_barokahart',
        'Produksi pot terakota estetik, guci hias indoor-outdoor, dan keramik dekorasi interior rumah.',
        'Pending',
    ],
    [
        'Aplikasi Kasir Digital Saudagar',
        'Irfan Kurniawan',
        'irfan.kasirsaudagar@gmail.com',
        'password123',
        '082467890110',
        'Aplikasi (Pengembangan Perangkat Lunak)',
        'Kabupaten Sleman',
        'Ngaglik',
        'Sariharjo',
        'Jl. Palagan Tentara Pelajar KM 9',
        '2023',
        '6',
        '65000000',
        '9120015566770',
        'Tidak',
        '',
        'Ya',
        '@kasir.saudagardigital',
        'Pengembangan Point of Sale (POS) cloud berbasis Android untuk merchant UMKM dan toko kelontong Surya Mart.',
        'Approved',
    ],
];

$rowIdx = 2;
foreach ($sampleData as $rowData) {
    $colLetter = 'A';
    foreach ($rowData as $colIndex => $val) {
        $cellCoord = $colLetter . $rowIdx;

        // WhatsApp (col 4, E) & NIB (col 13, N): Simpan eksplisit sebagai String teks agar awalan 0 tidak hilang
        if ($colLetter === 'E' || $colLetter === 'N') {
            $sheet->setCellValueExplicit($cellCoord, (string)$val, DataType::TYPE_STRING);
        } else {
            $sheet->setCellValue($cellCoord, $val);
        }

        $colLetter++;
    }

    // Border tipis dan zebra-striping halus
    $rowRange = 'A' . $rowIdx . ':T' . $rowIdx;
    $isEven = ($rowIdx % 2 === 0);
    $sheet->getStyle($rowRange)->applyFromArray([
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $isEven ? 'F9FAFB' : 'FFFFFF'],
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color'       => ['rgb' => 'E5E7EB'],
            ],
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getRowDimension($rowIdx)->setRowHeight(24);

    $rowIdx++;
}

// Auto-size kolom dengan padding
foreach (range('A', 'T') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Freeze baris header agar tetap tampak saat scrolling
$sheet->freezePane('A2');

// -------------------------------------------------------------
// SHEET 2: PANDUAN & DAFTAR REFERENSI
// -------------------------------------------------------------
$refSheet = $spreadsheet->createSheet();
$refSheet->setTitle('Panduan & Referensi');

$refSheet->setCellValue('A1', 'PANDUAN & CONTOH DATA UJI COBA IMPORT UMKM');
$refSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('15803D');

$instructions = [
    ['1. File ini berisi 10 data UMKM uji coba nyata di Daerah Istimewa Yogyakarta yang siap di-import langsung.'],
    ['2. Anda dapat langsung mengunggah file ini ke menu "Import Excel UMKM" di panel Admin.'],
    ['3. Seluruh email pada sheet pertama belum pernah terdaftar di sistem sehingga aman untuk di-import.'],
    ['4. Akun yang terbentuk akan memiliki password bawaan "password123" (kecuali jika diganti).'],
    ['5. Omzet Bulanan otomatis menentukan kategori usaha: < 25 Juta (Mikro), 25-208 Juta (Kecil), > 208 Juta (Menengah).'],
    ['6. Nomor WhatsApp dan NIB tersimpan rapi dalam format teks dengan awalan angka 0 utuh.'],
    ['7. Status Moderasi dapat bernilai "Approved" (langsung tampil di web) atau "Pending" (menunggu kurasi admin).'],
];

$insRow = 3;
foreach ($instructions as $ins) {
    $refSheet->setCellValue('A' . $insRow, $ins[0]);
    $insRow++;
}

// Daftar Subsektor EKRAF Referensi
$refSheet->setCellValue('A12', 'DAFTAR 21 SUBSEKTOR EKRAF RESMI:');
$refSheet->getStyle('A12')->getFont()->setBold(true);

$subsectors = Subsector::orderBy('id')->pluck('name')->toArray();
$subRow = 14;
foreach ($subsectors as $idx => $subName) {
    $refSheet->setCellValue('A' . $subRow, ($idx + 1) . '. ' . $subName);
    $subRow++;
}

// Wilayah DIY
$refSheet->setCellValue('D12', 'DAFTAR KABUPATEN / KOTA DI D.I. YOGYAKARTA:');
$refSheet->getStyle('D12')->getFont()->setBold(true);

$wilayahList = [
    'Kota Yogyakarta',
    'Kabupaten Sleman',
    'Kabupaten Bantul',
    'Kabupaten Kulon Progo',
    'Kabupaten Gunungkidul',
    'Luar Daerah Istimewa Yogyakarta',
];

$wilRow = 14;
foreach ($wilayahList as $idx => $wilName) {
    $refSheet->setCellValue('D' . $wilRow, ($idx + 1) . '. ' . $wilName);
    $wilRow++;
}

$refSheet->getColumnDimension('A')->setWidth(50);
$refSheet->getColumnDimension('D')->setWidth(40);

// Kembalikan lembar aktif ke Sheet 1
$spreadsheet->setActiveSheetIndex(0);

// Simpan ke direktori public/templates
$targetPath = __DIR__ . '/../public/templates/contoh-data-import-umkm.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($targetPath);

echo "File berhasil dibuat di: " . realpath($targetPath) . " (" . filesize($targetPath) . " bytes)\n";
