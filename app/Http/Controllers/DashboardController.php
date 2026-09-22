<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UmkmProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Threshold klasifikasi pendapatan per bulan (PP 7 / 2021)
    private const MIKRO_MAX    = 25_000_000;      // < Rp 25 juta/bulan  → < Rp 300 juta/tahun
    private const KECIL_MAX    = 208_333_333;     // < Rp 208 juta/bulan → < Rp 2,5 miliar/tahun

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $approved = UmkmProfile::where('status', 'approved');

            // --- Metrik Kartu ---
            $approvedCount = (clone $approved)->count();
            $pendingCount  = UmkmProfile::where('status', 'pending')->count();
            $totalProducts = Product::where('status', 'active')->count();
            $totalEmployees = (clone $approved)->sum('employee_count');

            // --- Grafik 1: Sertifikasi Halal ---
            $halalCount    = (clone $approved)->where('has_halal_certificate', true)->count();
            $nonHalalCount = $approvedCount - $halalCount;

            // --- Grafik 2: Klasifikasi UMKM berdasarkan pendapatan bulanan ---
            $mikro = (clone $approved)
                ->whereNotNull('monthly_revenue')
                ->where('monthly_revenue', '<', self::MIKRO_MAX)
                ->count();

            $kecil = (clone $approved)
                ->whereNotNull('monthly_revenue')
                ->whereBetween('monthly_revenue', [self::MIKRO_MAX, self::KECIL_MAX])
                ->count();

            $menengah = (clone $approved)
                ->whereNotNull('monthly_revenue')
                ->where('monthly_revenue', '>', self::KECIL_MAX)
                ->count();

            $belumIsiRevenue = (clone $approved)
                ->whereNull('monthly_revenue')
                ->count();

            return view('dashboard', [
                'approvedCount'    => $approvedCount,
                'pendingCount'     => $pendingCount,
                'totalProducts'    => $totalProducts,
                'totalEmployees'   => $totalEmployees,
                'halalCount'       => $halalCount,
                'nonHalalCount'    => $nonHalalCount,
                'mikro'            => $mikro,
                'kecil'            => $kecil,
                'menengah'         => $menengah,
                'belumIsiRevenue'  => $belumIsiRevenue,
            ]);
        }

        return view('dashboard', ['profile' => $user->umkmProfile]);
    }
}

