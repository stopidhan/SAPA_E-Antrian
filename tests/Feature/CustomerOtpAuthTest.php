<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Services\CustomerOtpNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class CustomerOtpAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_otp_with_full_name_and_whatsapp(): void
    {
        $this->createInstance();

        config(['services.turnstile.secret' => 'test-secret']);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        $capturedOtp = null;

        $expectedCanonicalPhone = '6281234567890';

        $this->mock(CustomerOtpNotificationService::class, function (MockInterface $mock) use (&$capturedOtp, $expectedCanonicalPhone): void {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnUsing(function (string $phone, string $plainOtpCode) use (&$capturedOtp, $expectedCanonicalPhone): void {
                    $capturedOtp = $plainOtpCode;
                    $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $plainOtpCode);
                    $this->assertSame($expectedCanonicalPhone, $phone);
                });
        });

        $response = $this->post(route('booking.register.submit', absolute: false), [
            'nama' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'cf-turnstile-response' => 'test-token',
        ]);

        $this->assertNotNull($capturedOtp);

        $response->assertRedirect(route('booking.otp.form', absolute: false));
        $response->assertSessionHas('status', 'Kode OTP berhasil dikirim ke WhatsApp Anda.');
        $response->assertSessionHas('customer_auth.pending_whatsapp', $expectedCanonicalPhone);
        $response->assertSessionHas('customer_auth.pending_name', 'Budi Santoso');

        $otpHash = session('customer_auth.otp_hash');
        $this->assertNotNull($otpHash);
        $this->assertTrue(Hash::check((string) $capturedOtp, (string) $otpHash));

        $this->assertNotNull(session('customer_auth.otp_expires_at'));
        $this->assertSame(0, (int) session('customer_auth.otp_attempts'));
    }

    public function test_customer_can_verify_otp_and_login(): void
    {
        $instance = $this->createInstance();

        $pendingPhone = '6281234567890';

        $response = $this->withSession([
            'customer_auth.pending_whatsapp' => $pendingPhone,
            'customer_auth.pending_name' => 'Budi Santoso',
            'customer_auth.otp_hash' => Hash::make('123456'),
            'customer_auth.otp_expires_at' => now()->addMinutes(5),
            'customer_auth.otp_attempts' => 0,
        ])->post(route('booking.otp.verify', absolute: false), [
            'whatsapp' => '081234567890',
            'otp_code' => '123456',
        ]);

        $response->assertRedirect(route('booking.dashboard', absolute: false));
        $this->assertAuthenticated('customer');

        $customer = \App\Models\Customer::query()->where('phone', $pendingPhone)->first();
        $this->assertNotNull($customer);
        $this->assertSame($instance->id, $customer->instance_id);
        $this->assertSame('Budi Santoso', $customer->name);
        $this->assertNotNull($customer->whatsapp_verified_at);
        $this->assertNotNull($customer->last_login_at);
    }

    private function createInstance(): Instance
    {
        return Instance::query()->create([
            'instance_code' => (string) Str::uuid(),
            'instance_name' => 'Disdukcapil Kudus',
        ]);
    }
}
