<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['pelatihan', 'pendampingan', 'workshop', 'seminar'])->default('pelatihan');
            $table->string('summary', 300);
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->dateTime('date_start');
            $table->dateTime('date_end')->nullable();
            $table->string('location');
            $table->string('organizer')->default('LP UMKM PWM DIY');
            $table->string('speaker')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->string('cost')->default('Gratis');
            $table->string('registration_url')->nullable();
            $table->string('whatsapp_contact')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
