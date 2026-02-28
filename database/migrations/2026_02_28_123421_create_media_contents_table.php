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
        Schema::create('media_contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('media_type');
            $table->string('file_path');

            $table->integer('duration')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_contents');
    }
};
