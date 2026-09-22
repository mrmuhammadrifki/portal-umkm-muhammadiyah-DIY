<?php

namespace Database\Seeders;

class DummyImageHelper
{
    /**
     * Palet warna bertema per kategori: [bg_r, bg_g, bg_b, accent_r, accent_g, accent_b]
     */
    public static array $categoryPalettes = [
        'Kuliner'   => ['bg' => [234, 88, 12],   'dark' => [154, 52, 18],  'accent' => [254, 215, 170]], // Warm Orange / Amber
        'Fashion'   => ['bg' => [147, 51, 234],  'dark' => [107, 33, 168], 'accent' => [243, 232, 255]], // Purple / Indigo
        'Kerajinan' => ['bg' => [13, 148, 136],  'dark' => [15, 118, 110], 'accent' => [204, 251, 241]], // Teal / Cyan
        'Jasa'      => ['bg' => [37, 99, 235],   'dark' => [30, 64, 175],  'accent' => [219, 234, 254]], // Blue
        'Pertanian' => ['bg' => [22, 163, 74],   'dark' => [21, 128, 61],  'accent' => [220, 252, 231]], // Emerald / Green
        'Lainnya'   => ['bg' => [75, 85, 99],    'dark' => [31, 41, 55],   'accent' => [243, 244, 246]], // Slate / Neutral
    ];

    /**
     * Pastikan direktori target ada.
     */
    private static function ensureDirectory(string $path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Buat dummy logo UMKM (300x300 PNG)
     */
    public static function createLogo(string $filename, string $businessName, string $category = 'Lainnya'): string
    {
        $dir = storage_path('app/public/umkm-logos');
        self::ensureDirectory($dir);
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $palette = self::$categoryPalettes[$category] ?? self::$categoryPalettes['Lainnya'];

        $w = 300;
        $h = 300;
        $img = imagecreatetruecolor($w, $h);

        // Background gradient effect
        $bg = imagecolorallocate($img, $palette['bg'][0], $palette['bg'][1], $palette['bg'][2]);
        $bgDark = imagecolorallocate($img, $palette['dark'][0], $palette['dark'][1], $palette['dark'][2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        $accent = imagecolorallocate($img, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2]);

        imagefill($img, 0, 0, $bg);

        // Outer border
        imagesetthickness($img, 6);
        imagerectangle($img, 10, 10, $w - 10, $h - 10, $bgDark);

        // Lingkaran tengah
        imagefilledellipse($img, $w / 2, 115, 120, 120, $bgDark);
        imageellipse($img, $w / 2, 115, 126, 126, $white);

        // Inisial usaha (1-3 huruf)
        $words = explode(' ', trim($businessName));
        $initials = '';
        foreach (array_slice($words, 0, 3) as $wItem) {
            $initials .= strtoupper(mb_substr($wItem, 0, 1));
        }

        // Teks inisial
        $font = 5;
        $initWidth = strlen($initials) * imagefontwidth($font) * 2;
        // Draw initials scaled up manually
        $charX = (int)(($w - (strlen($initials) * 18)) / 2);
        for ($i = 0; $i < strlen($initials); $i++) {
            imagestring($img, 5, $charX + ($i * 18), 105, $initials[$i], $white);
        }

        // Nama Usaha (dipotong jika panjang)
        $displayTitle = mb_strimwidth($businessName, 0, 24, '...');
        $titleX = (int)(($w - (strlen($displayTitle) * imagefontwidth(4))) / 2);
        imagestring($img, 4, max(15, $titleX), 195, $displayTitle, $white);

        // Badge Kategori
        $badgeText = strtoupper($category);
        $badgeX = (int)(($w - (strlen($badgeText) * imagefontwidth(2))) / 2);
        imagefilledrectangle($img, $badgeX - 10, 230, $badgeX + (strlen($badgeText) * imagefontwidth(2)) + 10, 252, $bgDark);
        imagestring($img, 2, $badgeX, 235, $badgeText, $accent);

        imagepng($img, $fullPath);
        imagedestroy($img);

        return 'umkm-logos/' . $filename;
    }

    /**
     * Buat dummy foto produk (800x600 PNG) dengan info galeri & kategori
     */
    public static function createProductImage(
        string $filename,
        string $productName,
        string $category = 'Lainnya',
        int $photoIndex = 1,
        int $totalPhotos = 1,
        string $label = 'Tampak Utama'
    ): string {
        $dir = storage_path('app/public/product-images');
        self::ensureDirectory($dir);
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $palette = self::$categoryPalettes[$category] ?? self::$categoryPalettes['Lainnya'];

        $w = 800;
        $h = 600;
        $img = imagecreatetruecolor($w, $h);

        $bg = imagecolorallocate($img, $palette['bg'][0], $palette['bg'][1], $palette['bg'][2]);
        $dark = imagecolorallocate($img, $palette['dark'][0], $palette['dark'][1], $palette['dark'][2]);
        $accent = imagecolorallocate($img, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        $cardBg = imagecolorallocate($img, 248, 250, 252);
        $textDark = imagecolorallocate($img, 15, 23, 42);
        $textMuted = imagecolorallocate($img, 100, 116, 139);

        // Background utama
        imagefill($img, 0, 0, $bg);

        // Pola aksen geometris di background
        for ($i = 0; $i < 6; $i++) {
            $col = imagecolorallocatealpha($img, $palette['dark'][0], $palette['dark'][1], $palette['dark'][2], 100);
            imagefilledellipse($img, 100 + ($i * 120), 80 + (($i % 2) * 40), 180, 180, $col);
        }

        // Card konten tengah (putih)
        imagefilledrectangle($img, 50, 60, $w - 50, $h - 60, $cardBg);
        imagesetthickness($img, 3);
        imagerectangle($img, 50, 60, $w - 50, $h - 60, $white);

        // Header Card: Badge Kategori
        $badgeStr = '  ' . strtoupper($category) . '  ';
        imagefilledrectangle($img, 80, 95, 80 + (strlen($badgeStr) * imagefontwidth(3)), 122, $bg);
        imagestring($img, 3, 85, 102, $badgeStr, $white);

        // Multi-photo indicator jika ada galeri
        if ($totalPhotos > 1) {
            $galleryStr = sprintf('Foto %d dari %d — %s', $photoIndex, $totalPhotos, $label);
            $strW = strlen($galleryStr) * imagefontwidth(3);
            imagefilledrectangle($img, $w - 90 - $strW, 95, $w - 80, 122, $dark);
            imagestring($img, 3, $w - 85 - $strW, 102, $galleryStr, $white);
        }

        // Kotak visual ilustrasi placeholder produk
        $boxX1 = 80;
        $boxY1 = 145;
        $boxX2 = $w - 80;
        $boxY2 = 410;

        imagefilledrectangle($img, $boxX1, $boxY1, $boxX2, $boxY2, $dark);

        // Ikon dekoratif di dalam kotak produk
        $centerX = (int)(($boxX1 + $boxX2) / 2);
        $centerY = (int)(($boxY1 + $boxY2) / 2) - 15;
        imagefilledellipse($img, $centerX, $centerY, 120, 120, $bg);
        imageellipse($img, $centerX, $centerY, 130, 130, $white);

        // Teks inisial atau nomor varian
        $centerLabel = $totalPhotos > 1 ? "#{$photoIndex}" : strtoupper(substr($category, 0, 3));
        $lblX = (int)($centerX - (strlen($centerLabel) * imagefontwidth(5) / 2));
        imagestring($img, 5, $lblX, $centerY - 8, $centerLabel, $white);

        // Nama produk di dalam kotak
        $displayProd = mb_strimwidth($productName, 0, 48, '...');
        $prodW = strlen($displayProd) * imagefontwidth(5);
        $prodX = max($boxX1 + 20, (int)(($w - $prodW) / 2));
        imagestring($img, 5, $prodX, $boxY2 - 45, $displayProd, $white);

        // Footer Card: Branding sistem & catatan
        $tagline = 'Portal UMKM Muhammadiyah DIY — Produk Resmi Terverifikasi';
        imagestring($img, 3, 80, 450, $tagline, $textDark);

        $contactNote = 'Hubungi langsung mitra UMKM via WhatsApp pada halaman detail.';
        imagestring($img, 2, 80, 480, $contactNote, $textMuted);

        // Titik-titik indikator foto di bagian bawah card
        if ($totalPhotos > 1) {
            $startX = (int)($w / 2 - (($totalPhotos * 24) / 2));
            for ($p = 1; $p <= $totalPhotos; $p++) {
                $dotX = $startX + (($p - 1) * 24);
                $dotColor = ($p === $photoIndex) ? $bg : $textMuted;
                imagefilledellipse($img, $dotX, 515, ($p === $photoIndex) ? 14 : 10, ($p === $photoIndex) ? 14 : 10, $dotColor);
            }
        }

        imagepng($img, $fullPath);
        imagedestroy($img);

        return 'product-images/' . $filename;
    }

    /**
     * Buat dummy banner event (1200x630 PNG)
     */
    public static function createEventBanner(
        string $filename,
        string $title,
        string $type = 'pelatihan',
        string $dateStr = 'Sabtu, 28 Oktober 2026',
        string $location = 'Gedung PWM DIY'
    ): string {
        $dir = storage_path('app/public/event-banners');
        self::ensureDirectory($dir);
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $typeColors = [
            'pelatihan'    => ['bg' => [30, 64, 175],  'accent' => [59, 130, 246], 'chip' => [219, 234, 254], 'text' => [30, 64, 175]],
            'pendampingan' => ['bg' => [4, 120, 87],   'accent' => [16, 185, 129], 'chip' => [209, 250, 229], 'text' => [4, 120, 87]],
            'workshop'     => ['bg' => [107, 33, 168], 'accent' => [168, 85, 247], 'chip' => [243, 232, 255], 'text' => [107, 33, 168]],
            'seminar'      => ['bg' => [180, 83, 9],   'accent' => [245, 158, 11], 'chip' => [254, 243, 199], 'text' => [180, 83, 9]],
        ];

        $palette = $typeColors[$type] ?? $typeColors['pelatihan'];

        $w = 1200;
        $h = 630;
        $img = imagecreatetruecolor($w, $h);

        $bg = imagecolorallocate($img, $palette['bg'][0], $palette['bg'][1], $palette['bg'][2]);
        $accent = imagecolorallocate($img, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2]);
        $white = imagecolorallocate($img, 255, 255, 255);
        $chipBg = imagecolorallocate($img, $palette['chip'][0], $palette['chip'][1], $palette['chip'][2]);
        $chipText = imagecolorallocate($img, $palette['text'][0], $palette['text'][1], $palette['text'][2]);
        $gold = imagecolorallocate($img, 251, 191, 36);

        imagefill($img, 0, 0, $bg);

        // Pattern dekorasi lingkaran modern
        for ($i = 0; $i < 8; $i++) {
            $col = imagecolorallocatealpha($img, $palette['accent'][0], $palette['accent'][1], $palette['accent'][2], 105);
            imagefilledellipse($img, 150 + ($i * 140), 100 + (($i % 2) * 200), 260, 260, $col);
        }

        // Inner border frame
        imagesetthickness($img, 4);
        imagerectangle($img, 24, 24, $w - 24, $h - 24, $accent);

        // Header penyelenggara
        $header = "LP UMKM PIMPINAN WILAYAH MUHAMMADIYAH DAERAH ISTIMEWA YOGYAKARTA";
        imagestring($img, 4, 60, 55, $header, $gold);

        // Badge Tipe
        $typeLabel = strtoupper(" AGENDA " . $type . " ");
        imagefilledrectangle($img, 60, 110, 60 + (strlen($typeLabel) * imagefontwidth(5)), 145, $chipBg);
        imagestring($img, 5, 65, 120, $typeLabel, $chipText);

        // Judul Kegiatan (wrap 2 baris jika panjang)
        $words = explode(' ', $title);
        $line1 = '';
        $line2 = '';
        foreach ($words as $wrd) {
            if (strlen($line1 . ' ' . $wrd) < 38 && empty($line2)) {
                $line1 .= ($line1 ? ' ' : '') . $wrd;
            } else {
                $line2 .= ($line2 ? ' ' : '') . $wrd;
            }
        }

        imagestring($img, 5, 60, 210, $line1, $white);
        if ($line2) {
            imagestring($img, 5, 60, 245, $line2, $white);
        }

        // Box info jadwal & lokasi
        imagefilledrectangle($img, 60, 360, $w - 60, 510, imagecolorallocatealpha($img, 0, 0, 0, 70));
        imagerectangle($img, 60, 360, $w - 60, 510, $accent);

        imagestring($img, 5, 90, 395, "WAKTU  : " . $dateStr, $white);
        imagestring($img, 5, 90, 445, "LOKASI : " . $location, $white);

        // Footer CTA
        $footer = "Informasi & Pendaftaran Resmi: portal-umkm.muhammadiyahdiy.or.id/agenda";
        imagestring($img, 3, 60, 560, $footer, $chipBg);

        imagepng($img, $fullPath);
        imagedestroy($img);

        return 'event-banners/' . $filename;
    }
}

