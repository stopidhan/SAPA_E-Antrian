<?php

namespace App\Services;

use App\Models\Customer;

class CustomerOtpNotificationService
{
    public function __construct(private readonly WablasService $wablasService)
    {
    }

    public function send(Customer $customer, string $plainOtpCode): void
    {
        $message = implode("\n", [
            'Kode OTP registrasi SAPA E-Antrian Anda: ' . $plainOtpCode,
            'Berlaku 5 menit. Jangan berikan kode ini ke siapa pun.',
        ]);

        $this->wablasService->sendMessage((string) $customer->phone, $message);
    }
}
