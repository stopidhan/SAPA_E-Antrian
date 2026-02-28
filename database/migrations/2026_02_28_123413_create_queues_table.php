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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_counter_id')->nullable()->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('service_category_id')->constrained();

            $table->string('queue_number');

            $table->date('queue_date');

            $table->time('taken_time')->nullable();
            $table->time('call_time')->nullable();
            $table->time('service_start_time')->nullable();
            $table->time('service_end_time')->nullable();

            $table->integer('service_duration')->nullable();

            $table->text('service_description')->nullable();

            $table->enum('queue_status', [
                'waiting',
                'called',
                'serving',
                'completed',
                'skipped'
            ])->default('waiting');

            $table->enum('queue_source', [
                'onsite',
                'online'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
