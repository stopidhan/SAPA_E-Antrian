<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WablasService
{
    public function __construct(
        private readonly ?string $token = null,
        private readonly ?string $baseUrl = null,
        private readonly ?string $endpoint = null,
    ) {
    }

    public function sendMessage(string $phone, string $message): void
    {
        $token = $this->token ?? (string) config('services.wablas.token', '');
        $baseUrl = $this->baseUrl ?? (string) config('services.wablas.base_url', '');
        $endpoint = $this->endpoint ?? (string) config('services.wablas.send_message_endpoint', 'api/send-message');

        if ($token === '' || $baseUrl === '') {
            throw new RuntimeException('Konfigurasi Wablas belum lengkap.');
        }

        $normalizedPhone = $this->normalizePhone($phone);

        Log::info('Wablas: mengirim pesan', [
            'phone' => $normalizedPhone,
            'base_url' => $baseUrl,
            'endpoint' => $endpoint,
        ]);

        $response = Http::baseUrl(rtrim($baseUrl, '/'))
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(15)
            ->withHeaders([
                'Authorization' => $token,
            ])
            ->post(ltrim($endpoint, '/'), [
                'phone' => $normalizedPhone,
                'message' => $message,
            ]);

        Log::info('Wablas: response diterima', [
            'http_status' => $response->status(),
            'body' => $response->body(),
        ]);

        $this->throwIfFailed($response);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }

    private function throwIfFailed(Response $response): void
    {
        $payload = $response->json();

        // Cek HTTP status code gagal
        if (!$response->successful()) {
            $message = is_array($payload)
                ? (string) ($payload['message'] ?? $payload['error'] ?? $response->body())
                : $response->body();

            Log::error('Wablas: HTTP error', [
                'status' => $response->status(),
                'message' => $message,
            ]);

            throw new RuntimeException('Gagal mengirim OTP WhatsApp via Wablas: ' . $message);
        }

        // Cek response body status dari Wablas (bisa HTTP 200 tapi status false)
        if (is_array($payload) && array_key_exists('status', $payload) && $payload['status'] === false) {
            $message = (string) ($payload['message'] ?? $payload['error'] ?? 'Status false tanpa pesan error.');

            Log::error('Wablas: API mengembalikan status false', [
                'payload' => $payload,
            ]);

            throw new RuntimeException('Gagal mengirim OTP WhatsApp via Wablas: ' . $message);
        }
    }
}
