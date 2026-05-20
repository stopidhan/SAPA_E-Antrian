<?php

namespace App\Services;

class CustomerOtpNotificationService
{
    public function __construct(private readonly WablasService $wablasService)
    {
    }

    public function send(string $phone, string $plainOtpCode): void
    {
        $message = implode("\n", [
            'Kode OTP registrasi SAPA E-Antrian Anda: ' . $plainOtpCode,
            'Berlaku 5 menit. Jangan berikan kode ini ke siapa pun.',
        ]);

        $this->wablasService->sendMessage($phone, $message);
    }
}
