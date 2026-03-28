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
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('whatsapp_verified_at')->nullable()->after('phone');
            $table->string('otp_code_hash')->nullable()->after('whatsapp_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code_hash');
            $table->unsignedTinyInteger('otp_attempts')->default(0)->after('otp_expires_at');
            $table->timestamp('otp_last_sent_at')->nullable()->after('otp_attempts');
            $table->timestamp('last_login_at')->nullable()->after('otp_last_sent_at');

            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'whatsapp_verified_at',
                'otp_code_hash',
                'otp_expires_at',
                'otp_attempts',
                'otp_last_sent_at',
                'last_login_at',
            ]);
        });
    }
};
