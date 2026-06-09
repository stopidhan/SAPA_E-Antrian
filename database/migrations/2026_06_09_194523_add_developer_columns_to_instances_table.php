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
        Schema::table('instances', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('id');
            $table->string('brand_color')->nullable()->after('is_active');
            $table->string('timezone')->default('Asia/Jakarta')->after('brand_color');
            $table->json('settings')->nullable()->after('timezone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'brand_color', 'timezone', 'settings']);
        });
    }
};
