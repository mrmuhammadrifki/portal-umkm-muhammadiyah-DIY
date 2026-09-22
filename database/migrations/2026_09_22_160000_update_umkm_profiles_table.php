<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('established_year')->nullable()->after('owner_name');
            $table->unsignedInteger('employee_count')->nullable()->after('established_year');
            $table->string('kelurahan')->nullable()->after('address');
            $table->boolean('has_halal_certificate')->default(false)->after('nib');
            $table->unsignedSmallInteger('halal_certificate_year')->nullable()->after('has_halal_certificate');
            $table->enum('has_attended_training', ['ya', 'tidak'])->default('tidak')->after('halal_certificate_year');

            if (Schema::hasColumn('umkm_profiles', 'affiliation_status')) {
                $table->dropColumn('affiliation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('umkm_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'established_year',
                'employee_count',
                'kelurahan',
                'has_halal_certificate',
                'halal_certificate_year',
                'has_attended_training',
            ]);

            $table->enum('affiliation_status', ['afiliasi', 'non_afiliasi'])->default('non_afiliasi')->after('nib');
        });
    }
};
