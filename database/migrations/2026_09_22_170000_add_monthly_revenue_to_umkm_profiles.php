<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm_profiles', function (Blueprint $table) {
            // Pendapatan rata-rata per bulan (dalam rupiah) — basis klasifikasi Mikro/Kecil/Menengah
            $table->unsignedBigInteger('monthly_revenue')->nullable()->after('employee_count')
                ->comment('Rata-rata pendapatan per bulan dalam rupiah (Mikro <25jt, Kecil 25-208jt, Menengah >208jt)');
        });
    }

    public function down(): void
    {
        Schema::table('umkm_profiles', function (Blueprint $table) {
            $table->dropColumn('monthly_revenue');
        });
    }
};
