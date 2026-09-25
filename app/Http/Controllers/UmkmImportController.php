<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subsector;
use App\Models\UmkmProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UmkmImportController extends Controller
{
    /**
     * Download template Excel (.xlsx) untuk import massal data UMKM
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        // -------------------------------------------------------------
        // SHEET 1: DATA UMKM (Template Input)
        // -------------------------------------------------------------
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import UMKM');

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

        // Styling Header (Brand Green #15803D, font bold putih, tinggi baris)
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

        // Contoh Data Baris 2, 3, 4 sebagai referensi pengguna
        $sampleData = [
            [
                'Bakpia Pathok Menoreh',
                'H. Ahmad Dahlan Saputra',
                'bakpia.menoreh.import@umkm.test',
                'password123',
                '081234567801',
                'Kuliner',
                'Kota Yogyakarta',
                'Gondomanan',
                'Ngupasan',
                'Jl. Kauman No. 42',
                '2018',
                '8',
                '22000000',
                '9120001234567',
                'Ya',
                '2022',
                'Ya',
                '@bakpia_menoreh',
                'Produsen bakpia pathok resep turun-temurun khas Yogyakarta dengan kacang hijau pilihan.',
                'Approved',
            ],
            [
                'Batik Tulis Surya Malioboro',
                'Siti Walidah Handayani',
                'batik.surya.import@umkm.test',
                'password123',
                '081234567802',
                'Fashion',
                'Kabupaten Sleman',
                'Mlati',
                'Sinduharjo',
                'Jl. Kaliurang KM 8',
                '2020',
                '5',
                '35000000',
                '9120002345678',
                'Tidak',
                '',
                'Ya',
                '@batiktulis_surya',
                'Sentra kerajinan batik tulis halus khas Surya Muhammadiyah dengan pewarna alami.',
                'Approved',
            ],
            [
                'Kotagede Silver & Filigree Art',
                'Bambang Sudarsono',
                'silver.kotagede.import@umkm.test',
                '',
                '081234567803',
                'Kriya',
                'Kabupaten Bantul',
                'Banguntapan',
                'Jagalan',
                'Jl. Mondorakan No. 78',
                '2019',
                '3',
                '14000000',
                '',
                'Tidak',
                '',
                'Tidak',
                '@kotagede_silver',
                'Bengkel kriya perak asli Kotagede spesialis teknik filigree souvenir dan perhiasan.',
                'Approved',
            ],
        ];

        $rowIdx = 2;
        foreach ($sampleData as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                // Pastikan kolom angka seperti WhatsApp, NIB, dan Phone bertipe String teks agar awalan 0 tidak hilang
                if (in_array($colLetter, ['E', 'N'])) {
                    $sheet->setCellValueExplicit($colLetter . $rowIdx, (string) $val, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter . $rowIdx, $val);
                }
                $colLetter++;
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(24);
            $rowIdx++;
        }

        // Border tipis pada contoh data
        $sheet->getStyle('A2:T4')->applyFromArray([
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

        // Auto-width tiap kolom
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // -------------------------------------------------------------
        // SHEET 2: PETUNJUK & DAFTAR REFERENSI
        // -------------------------------------------------------------
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Petunjuk & Referensi');

        $refSheet->setCellValue('A1', 'PANDUAN PENGISIAN TEMPLATE IMPORT DATA UMKM');
        $refSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('15803D');

        $instructions = [
            ['1. Kolom dengan tanda bintang (*) wajib diisi: Nama Usaha, Nama Pemilik, Email Akun, dan Nomor WhatsApp.'],
            ['2. Email Akun harus unik (belum pernah terdaftar di sistem) karena digunakan sebagai username login pemilik UMKM.'],
            ['3. Password Akun opsional: jika dikosongkan, sistem otomatis memberikan password default "password123".'],
            ['4. Nomor WhatsApp diawali dengan angka 0 atau 62 (contoh: 081234567890). Kolom sudah diformat sebagai teks.'],
            ['5. Omzet Bulanan diisi angka bulat tanpa titik/koma/Rp (contoh: 15000000). Sistem otomatis mengklasifikasikan skala usaha (Mikro/Kecil/Menengah).'],
            ['6. Sertifikat Halal & Pernah Pelatihan diisi dengan "Ya" atau "Tidak". Jika Halal = Ya, wajib mengisi Tahun Sertifikat Halal.'],
            ['7. Status Moderasi: isi "Approved" agar langsung tayang di katalog publik, atau "Pending" untuk verifikasi manual.'],
            ['8. Anda dapat menghapus 3 baris data contoh di sheet pertama sebelum mengunggah file Anda.'],
        ];

        $insRow = 3;
        foreach ($instructions as $ins) {
            $refSheet->setCellValue('A' . $insRow, $ins[0]);
            $insRow++;
        }

        // Daftar Subsektor EKRAF Referensi
        $refSheet->setCellValue('A13', 'DAFTAR REFERENSI 21 SUBSEKTOR EKRAF:');
        $refSheet->getStyle('A13')->getFont()->setBold(true);

        $subsectors = Subsector::orderBy('id')->pluck('name')->toArray();
        if (empty($subsectors)) {
            $subsectors = [
                'Kuliner', 'Fashion', 'Kriya', 'Desain Produk', 'Seni Rupa', 'Penerbitan',
                'Fotografi', 'Musik', 'Periklanan', 'Aplikasi & Game', 'Desain Komunikasi Visual',
            ];
        }

        $subRow = 15;
        foreach ($subsectors as $idx => $subName) {
            $refSheet->setCellValue('A' . $subRow, ($idx + 1) . '. ' . $subName);
            $subRow++;
        }

        // Daftar Wilayah Kabupaten/Kota DIY
        $refSheet->setCellValue('D13', 'DAFTAR KABUPATEN / KOTA DI D.I. YOGYAKARTA:');
        $refSheet->getStyle('D13')->getFont()->setBold(true);

        $wilayahList = [
            'Kota Yogyakarta',
            'Kabupaten Sleman',
            'Kabupaten Bantul',
            'Kabupaten Kulon Progo',
            'Kabupaten Gunungkidul',
            'Luar Daerah Istimewa Yogyakarta',
        ];

        $wilRow = 15;
        foreach ($wilayahList as $idx => $wilName) {
            $refSheet->setCellValue('D' . $wilRow, ($idx + 1) . '. ' . $wilName);
            $wilRow++;
        }

        $refSheet->getColumnDimension('A')->setWidth(50);
        $refSheet->getColumnDimension('D')->setWidth(40);

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'template-import-data-umkm.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Download file contoh data UMKM (.xlsx) siap uji coba (10 data lengkap)
     */
    public function downloadSampleData(): StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filePath = public_path('templates/contoh-data-import-umkm.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'contoh-data-import-umkm.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Fallback jika file fisik belum ada: arahkan ke downloadTemplate
        return $this->downloadTemplate();
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'file.required' => 'Silakan pilih file Excel template yang ingin diimpor.',
            'file.mimes'    => 'Format file harus berupa Excel (.xlsx atau .xls).',
            'file.max'      => 'Ukuran file Excel maksimal 10MB.',
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) <= 1) {
            return redirect()->back()->with('error', 'File Excel kosong atau hanya berisi baris header.');
        }

        $importedCount = 0;
        $skippedCount  = 0;
        $errors        = [];

        // Cache daftar subsektor untuk pencarian cepat
        $subsectors = Subsector::all();

        // Lewati baris 1 (Header), mulai baca dari baris 2
        for ($rowNum = 2; $rowNum <= count($rows); $rowNum++) {
            $row = $rows[$rowNum] ?? null;
            if (! $row) {
                continue;
            }

            // Ambil dan rapikan nilai kolom
            $businessName   = trim($row['A'] ?? '');
            $ownerName      = trim($row['B'] ?? '');
            $email          = strtolower(trim($row['C'] ?? ''));
            $passwordRaw    = trim($row['D'] ?? '');
            $whatsapp       = trim($row['E'] ?? '');
            $subsectorInput = trim($row['F'] ?? '');
            $kabupatenKota  = trim($row['G'] ?? '');
            $kecamatan      = trim($row['H'] ?? '');
            $kelurahan      = trim($row['I'] ?? '');
            $address        = trim($row['J'] ?? '');
            $estYear        = trim($row['K'] ?? '');
            $empCount       = trim($row['L'] ?? '');
            $revenue        = trim($row['M'] ?? '');
            $nib            = trim($row['N'] ?? '');
            $halalRaw       = strtolower(trim($row['O'] ?? ''));
            $halalYear      = trim($row['P'] ?? '');
            $trainingRaw    = strtolower(trim($row['Q'] ?? ''));
            $instagram      = trim($row['R'] ?? '');
            $description    = trim($row['S'] ?? '');
            $statusRaw      = strtolower(trim($row['T'] ?? ''));

            // Abaikan jika seluruh kolom baris ini kosong
            if ($businessName === '' && $ownerName === '' && $email === '' && $whatsapp === '') {
                continue;
            }

            // Validasi data wajib
            if ($businessName === '' || $ownerName === '' || $email === '' || $whatsapp === '') {
                $errors[] = "Baris {$rowNum}: Kolom Nama Usaha, Pemilik, Email, dan WhatsApp wajib diisi.";
                $skippedCount++;
                continue;
            }

            // Validasi format email
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Baris {$rowNum}: Format email '{$email}' tidak valid.";
                $skippedCount++;
                continue;
            }

            // Cek duplikasi email pada database
            if (User::where('email', $email)->exists()) {
                $errors[] = "Baris {$rowNum}: Email '{$email}' sudah terdaftar dalam sistem (dilewati).";
                $skippedCount++;
                continue;
            }

            // Bersihkan nomor WhatsApp (hanya angka)
            $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
            if (strlen($cleanWhatsapp) < 9) {
                $errors[] = "Baris {$rowNum}: Nomor WhatsApp '{$whatsapp}' tidak valid (minimal 9 digit).";
                $skippedCount++;
                continue;
            }

            // Cari subsektor_id yang cocok jika diisi
            $subsectorId = null;
            if ($subsectorInput !== '') {
                $matchedSubsector = $subsectors->first(function ($s) use ($subsectorInput) {
                    return stripos($s->name, $subsectorInput) !== false || stripos($subsectorInput, $s->name) !== false;
                });
                $subsectorId = $matchedSubsector?->id;
            }

            // Parsing boolean halal
            $hasHalal = in_array($halalRaw, ['ya', '1', 'sudah', 'true', 'yes'], true);
            $parsedHalalYear = ($hasHalal && is_numeric($halalYear) && (int) $halalYear >= 1980) ? (int) $halalYear : null;

            // Parsing pelatihan
            $hasAttended = in_array($trainingRaw, ['ya', '1', 'pernah', 'true', 'yes'], true) ? 'ya' : 'tidak';

            // Parsing status moderasi
            $status = in_array($statusRaw, ['pending', 'rejected', 'ditolak', 'menunggu'], true)
                ? ($statusRaw === 'ditolak' ? 'rejected' : 'pending')
                : 'approved';

            // Parsing angka numerik
            $parsedEstYear = (is_numeric($estYear) && (int) $estYear >= 1900 && (int) $estYear <= (int) date('Y')) ? (int) $estYear : null;
            $parsedEmpCount = (is_numeric($empCount) && (int) $empCount >= 0) ? (int) $empCount : null;
            $parsedRevenue  = (is_numeric($revenue) && (int) $revenue >= 0) ? (int) $revenue : null;

            // Tentukan category_id dinamis berdasarkan omzet (Mikro, Kecil, Menengah) jika kategori tersedia
            $categoryId = null;
            if ($parsedRevenue !== null) {
                if ($parsedRevenue < 25_000_000) {
                    $slug = 'mikro';
                } elseif ($parsedRevenue <= 208_333_333) {
                    $slug = 'kecil';
                } else {
                    $slug = 'menengah';
                }
                $categoryId = Category::where('slug', $slug)->value('id');
            }

            // Password default jika kosong: 'password123'
            $password = $passwordRaw !== '' ? $passwordRaw : 'password123';

            // Eksekusi insert User & UmkmProfile dalam 1 transaksi
            DB::transaction(function () use (
                $ownerName, $email, $password, $cleanWhatsapp,
                $businessName, $description, $address, $kelurahan, $kecamatan, $kabupatenKota,
                $instagram, $nib, $hasHalal, $parsedHalalYear, $hasAttended,
                $parsedEstYear, $parsedEmpCount, $parsedRevenue,
                $subsectorId, $categoryId, $status
            ) {
                $user = User::create([
                    'name'      => $ownerName,
                    'email'     => $email,
                    'password'  => Hash::make($password),
                    'role'      => 'umkm',
                    'phone'     => $cleanWhatsapp,
                    'is_active' => true,
                ]);

                UmkmProfile::create([
                    'user_id'                => $user->id,
                    'business_name'          => $businessName,
                    'owner_name'             => $ownerName,
                    'established_year'       => $parsedEstYear,
                    'employee_count'         => $parsedEmpCount,
                    'monthly_revenue'        => $parsedRevenue,
                    'description'            => $description ?: null,
                    'address'                => $address ?: null,
                    'kelurahan'              => $kelurahan ?: null,
                    'kecamatan'              => $kecamatan ?: null,
                    'kabupaten_kota'         => $kabupatenKota ?: null,
                    'whatsapp'               => $cleanWhatsapp,
                    'instagram'              => $instagram ?: null,
                    'nib'                    => $nib ?: null,
                    'has_halal_certificate'  => $hasHalal,
                    'halal_certificate_year' => $parsedHalalYear,
                    'has_attended_training'  => $hasAttended,
                    'subsector_id'           => $subsectorId,
                    'category_id'            => $categoryId,
                    'status'                 => $status,
                ]);
            });

            $importedCount++;
        }

        $message = "Proses import selesai. Berhasil menambahkan {$importedCount} data UMKM baru.";
        if ($skippedCount > 0) {
            $message .= " Sebanyak {$skippedCount} baris data dilewati.";
        }

        return redirect()->route('admin.umkm-profiles.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
