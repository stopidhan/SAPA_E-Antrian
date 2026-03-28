<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Instance;
use App\Services\CustomerOtpNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class CustomerOtpAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_otp_with_full_name_and_whatsapp(): void
    {
        $this->createInstance();

        $capturedOtp = null;

        $this->mock(CustomerOtpNotificationService::class, function (MockInterface $mock) use (&$capturedOtp): void {
            $mock->shouldReceive('send')
                ->once()
                ->andReturnUsing(function (Customer $customer, string $plainOtpCode) use (&$capturedOtp): void {
                    $capturedOtp = $plainOtpCode;
                    $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $plainOtpCode);
                    $this->assertSame('Budi Santoso', $customer->name);
                    $this->assertSame('081234567890', $customer->phone);
                });
        });

        $response = $this->post(route('booking.register.submit', absolute: false), [
            'nama' => 'Budi Santoso',
            'whatsapp' => '081234567890',
        ]);

        $customer = Customer::query()->where('phone', '081234567890')->first();

        $this->assertNotNull($customer);
        $this->assertNotNull($capturedOtp);
        $this->assertTrue(Hash::check((string) $capturedOtp, (string) $customer->otp_code_hash));
        $this->assertNotNull($customer->otp_expires_at);
        $this->assertSame(0, (int) $customer->otp_attempts);

        $response->assertRedirect(route('booking.otp.form', absolute: false));
        $response->assertSessionHas('status', 'Kode OTP berhasil dikirim ke WhatsApp Anda.');
        $response->assertSessionHas('customer_auth.pending_customer_id', $customer->id);
        $response->assertSessionHas('customer_auth.pending_whatsapp', '081234567890');
    }

    public function test_customer_can_verify_otp_and_login(): void
    {
        $instance = $this->createInstance();

        $customer = Customer::query()->create([
            'instance_id' => $instance->id,
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'otp_code_hash' => Hash::make('123456'),
            'otp_expires_at' => now()->addMinutes(5),
            'otp_attempts' => 0,
            'otp_last_sent_at' => now(),
        ]);

        $response = $this->withSession([
            'customer_auth.pending_customer_id' => $customer->id,
            'customer_auth.pending_whatsapp' => $customer->phone,
        ])->post(route('booking.otp.verify', absolute: false), [
            'whatsapp' => '081234567890',
            'otp_code' => '123456',
        ]);

        $response->assertRedirect(route('booking.dashboard', absolute: false));
        $this->assertAuthenticated('customer');

        $customer->refresh();

        $this->assertNull($customer->otp_code_hash);
        $this->assertNull($customer->otp_expires_at);
        $this->assertSame(0, (int) $customer->otp_attempts);
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
