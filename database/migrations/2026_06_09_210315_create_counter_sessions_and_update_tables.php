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
        Schema::create('counter_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_counter_id')->constrained('service_counters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('service_counter_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('service_counters', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_counters', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('service_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('queues', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('counter_sessions');
    }
};
