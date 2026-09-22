<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'status')) {
                $table->dropColumn('status');
            }
            $table->string('summary', 300)->nullable()->change();
            if (!Schema::hasColumn('events', 'contact_person')) {
                $table->string('contact_person', 255)->nullable()->after('location');
            }
        });

        // Copy any existing whatsapp_contact into contact_person if available
        if (Schema::hasColumn('events', 'whatsapp_contact')) {
            DB::statement("UPDATE events SET contact_person = whatsapp_contact WHERE (contact_person IS NULL OR contact_person = '') AND whatsapp_contact IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            if (Schema::hasColumn('events', 'contact_person')) {
                $table->dropColumn('contact_person');
            }
        });
    }
};
