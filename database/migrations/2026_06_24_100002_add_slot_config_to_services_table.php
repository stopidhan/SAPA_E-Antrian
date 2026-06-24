<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom konfigurasi slot waktu ke tabel services.
     * Kolom ini dikelola oleh Admin Instansi melalui halaman Manajemen Layanan.
     *
     * - slot_duration : Durasi setiap slot waktu (menit). Contoh: 60 = slot 1 jam.
     * - slot_capacity : Jumlah maksimal orang yang bisa booking per slot.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedSmallInteger('slot_duration')->default(60)->after('description')
                  ->comment('Durasi per slot waktu dalam menit (diatur Admin Instansi)');
            $table->unsignedSmallInteger('slot_capacity')->default(10)->after('slot_duration')
                  ->comment('Kapasitas maksimal booking per slot (diatur Admin Instansi)');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['slot_duration', 'slot_capacity']);
        });
    }
};
