<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerOtpNotificationService
{
    public function send(Customer $customer, string $plainOtpCode): void
    {
        // Simulasi pengiriman OTP. Integrasi API WhatsApp bisa dipindah ke sini.
        Log::info('Customer OTP sent', [
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'otp_code' => $plainOtpCode,
        ]);
    }
}
