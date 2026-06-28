<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi pembuatan tabel service_slots.
     * Tabel ini digunakan untuk menyimpan slot waktu pelayanan kustom
     * beserta kapasitasnya yang diatur oleh Admin Instansi.
     */
    public function up(): void
    {
        Schema::create('service_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('start_time', 5)->comment('Jam mulai slot (format HH:MM, contoh: 08:00)');
            $table->string('end_time', 5)->comment('Jam selesai slot (format HH:MM, contoh: 09:00)');
            $table->unsignedInteger('capacity')->default(5)->comment('Kapasitas maksimal booking untuk slot ini');
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_slots');
    }
};
