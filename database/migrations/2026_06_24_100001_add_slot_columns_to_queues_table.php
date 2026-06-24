<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom scheduled_date dan scheduled_slot ke tabel queues.
     * Digunakan untuk sistem booking online bergaya M-Paspor (pilih slot waktu).
     *
     * - scheduled_date : Tanggal yang dipilih pengunjung saat booking
     * - scheduled_slot : Jam slot yang dipilih (format HH:MM, contoh: "09:00")
     */
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable()->after('queue_date')
                  ->comment('Tanggal booking slot — diisi untuk antrean online');
            $table->string('scheduled_slot', 5)->nullable()->after('scheduled_date')
                  ->comment('Jam slot booking (HH:MM) — diisi untuk antrean online');
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn(['scheduled_date', 'scheduled_slot']);
        });
    }
};
