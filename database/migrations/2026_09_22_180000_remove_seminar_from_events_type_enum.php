<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing events with type 'seminar' to 'workshop'
        DB::table('events')->where('type', 'seminar')->update(['type' => 'workshop']);

        // Modify enum to only pelatihan, pendampingan, workshop
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('pelatihan', 'pendampingan', 'workshop') NOT NULL DEFAULT 'pelatihan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('pelatihan', 'pendampingan', 'workshop', 'seminar') NOT NULL DEFAULT 'pelatihan'");
    }
};
