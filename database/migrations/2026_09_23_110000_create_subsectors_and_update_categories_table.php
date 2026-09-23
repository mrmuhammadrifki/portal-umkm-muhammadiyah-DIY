<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel subsectors (21 Subsektor Ekonomi Kreatif EKRAF)
        Schema::create('subsectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Tambah kolom batas pendapatan & deskripsi pada categories (Skala Usaha: Mikro, Kecil, Menengah)
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('min_revenue')->nullable()->after('slug');
            $table->unsignedBigInteger('max_revenue')->nullable()->after('min_revenue');
            $table->string('description')->nullable()->after('max_revenue');
        });

        // 3. Tambah relasi subsector_id pada products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('subsector_id')->nullable()->after('umkm_id')->constrained('subsectors')->nullOnDelete();
            // Drop foreign key lama pada category_id agar bisa nullable
            $table->dropForeign(['category_id']);
            $table->foreignId('category_id')->nullable()->change()->constrained('categories')->nullOnDelete();
        });

        // 4. Tambah relasi subsector_id & category_id pada umkm_profiles
        Schema::table('umkm_profiles', function (Blueprint $table) {
            $table->foreignId('subsector_id')->nullable()->after('description')->constrained('subsectors')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('subsector_id')->constrained('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('umkm_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('subsector_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subsector_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['min_revenue', 'max_revenue', 'description']);
        });

        Schema::dropIfExists('subsectors');
    }
};
