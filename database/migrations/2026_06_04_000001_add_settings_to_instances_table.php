<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->boolean('tts_enabled')->default(true)->after('logo');
            $table->unsignedInteger('max_bookings_per_day')->default(5)->after('tts_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn(['tts_enabled', 'max_bookings_per_day']);
        });
    }
};
