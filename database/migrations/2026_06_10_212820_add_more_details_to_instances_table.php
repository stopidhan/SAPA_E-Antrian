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
            $table->string('favicon')->nullable()->after('logo');
            $table->string('secondary_color')->nullable()->after('brand_color');
            $table->string('latitude')->nullable()->after('address');
            $table->string('longitude')->nullable()->after('latitude');
            $table->json('operational_hours')->nullable()->after('timezone');
            $table->string('tts_language')->default('id-ID')->after('tts_enabled');
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->string('instagram')->nullable()->after('website');
            $table->string('facebook')->nullable()->after('instagram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn([
                'favicon',
                'secondary_color',
                'latitude',
                'longitude',
                'operational_hours',
                'tts_language',
                'whatsapp_number',
                'instagram',
                'facebook',
            ]);
        });
    }
};
