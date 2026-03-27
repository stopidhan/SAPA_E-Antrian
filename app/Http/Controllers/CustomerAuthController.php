<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAuth\SendCustomerOtpRequest;
use App\Http\Requests\CustomerAuth\VerifyCustomerOtpRequest;
use App\Models\Customer;
use App\Models\Instance;
use App\Services\CustomerOtpNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    private const OTP_EXPIRES_IN_MINUTES = 5;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;
    private const OTP_MAX_ATTEMPTS = 5;

    public function __construct(private readonly CustomerOtpNotificationService $otpNotificationService)
    {
    }

    public function showLoginForm(): View
    {
        return view('Pages.Remoteuser.Login');
    }

    public function sendOtp(SendCustomerOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $phone = $validated['whatsapp'];

        $customer = Customer::query()->where('phone', $phone)->first();

        if ($customer) {
            if ($customer->name !== $validated['nama']) {
                $customer->update([
                    'name' => $validated['nama'],
                ]);
            }

            if ($customer->otp_last_sent_at && now()->diffInSeconds($customer->otp_last_sent_at) < self::OTP_RESEND_COOLDOWN_SECONDS) {
                $waitSeconds = self::OTP_RESEND_COOLDOWN_SECONDS - now()->diffInSeconds($customer->otp_last_sent_at);

                throw ValidationException::withMessages([
                    'whatsapp' => 'OTP baru bisa dikirim ulang dalam ' . $waitSeconds . ' detik.',
                ]);
            }
        } else {
            $instanceId = Instance::query()->value('id');

            if (!$instanceId) {
                throw ValidationException::withMessages([
                    'whatsapp' => 'Data instansi belum tersedia. Hubungi admin terlebih dahulu.',
                ]);
            }

            $customer = Customer::query()->create([
                'instance_id' => $instanceId,
                'name' => $validated['nama'],
                'phone' => $phone,
            ]);
        }

        $plainOtpCode = (string) random_int(100000, 999999);

        $customer->forceFill([
            'otp_code_hash' => $plainOtpCode,
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRES_IN_MINUTES),
            'otp_attempts' => 0,
            'otp_last_sent_at' => now(),
        ])->save();

        $this->otpNotificationService->send($customer, $plainOtpCode);

        Session::put('customer_auth.pending_customer_id', $customer->id);
        Session::put('customer_auth.pending_whatsapp', $customer->phone);

        return redirect()->route('booking.otp.form')
            ->with('status', 'Kode OTP berhasil dikirim ke WhatsApp Anda (simulasi).');
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        $pendingPhone = (string) Session::get('customer_auth.pending_whatsapp', '');

        if ($pendingPhone === '') {
            return redirect()->route('booking.register')
                ->withErrors(['booking_register' => 'Sesi verifikasi tidak ditemukan. Silakan login kembali.']);
        }

        return view('Pages.Remoteuser.VerifyOtp', [
            'pendingPhone' => $pendingPhone,
        ]);
    }

    public function verifyOtp(VerifyCustomerOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $pendingCustomerId = (int) Session::get('customer_auth.pending_customer_id', 0);
        $pendingPhone = (string) Session::get('customer_auth.pending_whatsapp', '');

        $customer = Customer::query()
            ->where('id', $pendingCustomerId)
            ->where('phone', $pendingPhone)
            ->first();

        if (!$customer || $customer->phone !== $validated['whatsapp']) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Sesi OTP tidak valid. Silakan login ulang.',
            ]);
        }

        if (!$customer->otp_code_hash || !$customer->otp_expires_at || now()->gt($customer->otp_expires_at)) {
            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.',
            ]);
        }

        if ((int) $customer->otp_attempts >= self::OTP_MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'otp_code' => 'Percobaan OTP melebihi batas. Silakan kirim ulang OTP.',
            ]);
        }

        if ((string) $validated['otp_code'] !== (string) $customer->otp_code_hash) {
            $customer->increment('otp_attempts');

            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP tidak valid.',
            ]);
        }

        $customer->forceFill([
            'otp_code_hash' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'whatsapp_verified_at' => now(),
            'last_login_at' => now(),
        ])->save();

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        Session::forget('customer_auth.pending_customer_id');
        Session::forget('customer_auth.pending_whatsapp');
        Session::forget('url.intended');

        return redirect()->route('booking.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('booking.register');
    }
}
