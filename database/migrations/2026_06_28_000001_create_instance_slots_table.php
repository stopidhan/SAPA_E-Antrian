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
        Schema::create('instance_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained('instances')->cascadeOnDelete();
            
            $table->string('start_time', 5)->comment('Jam mulai slot (format HH:MM, contoh: 08:00)');
            $table->string('end_time', 5)->comment('Jam selesai slot (format HH:MM, contoh: 09:00)');
            $table->unsignedInteger('capacity')->default(10)->comment('Kapasitas maksimal booking online untuk slot ini');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_slots');
    }
};
