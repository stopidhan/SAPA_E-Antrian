<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE queues MODIFY COLUMN queue_status ENUM('waiting', 'called', 'serving', 'completed', 'skipped', 'cancelled') DEFAULT 'waiting'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE queues MODIFY COLUMN queue_status ENUM('waiting', 'called', 'serving', 'completed', 'skipped') DEFAULT 'waiting'");
    }
};
