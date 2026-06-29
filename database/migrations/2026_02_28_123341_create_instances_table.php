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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->string('brand_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('timezone')->default('Asia/Jakarta');
            $table->json('operational_hours')->nullable();
            $table->json('settings')->nullable();
            $table->uuid('instance_code')->unique();
            $table->string('instance_slug')->nullable()->unique();
            $table->string('instance_name');
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->boolean('tts_enabled')->default(true);
            $table->string('tts_language')->default('id-ID');
            $table->unsignedInteger('max_offline_bookings_per_day')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
