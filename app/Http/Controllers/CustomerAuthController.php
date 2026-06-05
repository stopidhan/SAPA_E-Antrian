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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Throwable;
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

    public function showRegisterForm(): View
    {
        return view('Pages.Remoteuser.Register');
    }

    public function register(SendCustomerOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $inputName = trim((string) $validated['nama']);
        $phone = $this->normalizePhoneDigits((string) $validated['whatsapp']);
        $plainOtpCode = (string) random_int(100000, 999999);

        try {
            $instanceCode = $request->route('instance_code');
            $instance = Instance::where('instance_code', $instanceCode)->first();

            if (!$instance) {
                throw ValidationException::withMessages([
                    'whatsapp' => 'Data instansi tidak ditemukan pada URL ini.',
                ]);
            }

            $customer = $this->findCustomerByPhone($phone, $instance->id);

            $rateLimitKey = 'send_otp_' . $phone;
            if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
                $waitSeconds = RateLimiter::availableIn($rateLimitKey);
                throw ValidationException::withMessages([
                    'whatsapp' => 'OTP baru bisa dikirim ulang dalam ' . $waitSeconds . ' detik.',
                ]);
            }

            $this->otpNotificationService->send($phone, $plainOtpCode);
            RateLimiter::hit($rateLimitKey, self::OTP_RESEND_COOLDOWN_SECONDS);

            Session::put('customer_auth.pending_whatsapp', $phone);
            Session::put('customer_auth.pending_name', $inputName);
            Session::put('customer_auth.otp_hash', Hash::make($plainOtpCode));
            Session::put('customer_auth.otp_expires_at', now()->addMinutes(self::OTP_EXPIRES_IN_MINUTES));
            Session::put('customer_auth.otp_attempts', 0);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('OTP WhatsApp gagal dikirim', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            $publicReason = $this->extractPublicOtpSendErrorReason($e->getMessage());

            throw ValidationException::withMessages([
                'whatsapp' => 'OTP gagal dikirim ke WhatsApp. Silakan coba lagi.' . ($publicReason !== '' ? ' (' . $publicReason . ')' : ''),
            ]);
        }

        return redirect()->route('booking.otp.form')
            ->with('status', 'Kode OTP berhasil dikirim ke WhatsApp Anda.');
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
        
        $pendingPhone = (string) Session::get('customer_auth.pending_whatsapp', '');
        $pendingName = (string) Session::get('customer_auth.pending_name', '');
        $otpHash = Session::get('customer_auth.otp_hash');
        $otpExpiresAt = Session::get('customer_auth.otp_expires_at');
        $otpAttempts = (int) Session::get('customer_auth.otp_attempts', 0);

        if ($pendingPhone === '' || $pendingPhone !== $validated['whatsapp'] || !$otpHash) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Sesi OTP tidak valid. Silakan login ulang.',
            ]);
        }

        if (!$otpExpiresAt || now()->gt($otpExpiresAt)) {
            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.',
            ]);
        }

        if ($otpAttempts >= self::OTP_MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'otp_code' => 'Percobaan OTP melebihi batas. Silakan kirim ulang OTP.',
            ]);
        }

        if (!Hash::check((string) $validated['otp_code'], (string) $otpHash)) {
            Session::put('customer_auth.otp_attempts', $otpAttempts + 1);
            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP tidak valid.',
            ]);
        }

            $instanceCode = $request->route('instance_code');
            $instance = Instance::where('instance_code', $instanceCode)->first();
            $instanceId = $instance ? $instance->id : null;
            
            if (!$instanceId) {
                throw ValidationException::withMessages([
                    'otp_code' => 'Instansi tidak ditemukan.',
                ]);
            }

        $customer = $this->findCustomerByPhone($pendingPhone, $instanceId);

        if (!$customer) {
            
            $customer = Customer::query()->create([
                'instance_id' => $instanceId,
                'name' => $pendingName,
                'phone' => $pendingPhone,
                'whatsapp_verified_at' => now(),
                'last_login_at' => now(),
                'otp_attempts' => 0,
            ]);
        } else {
            $customer->update([
                'whatsapp_verified_at' => now(),
                'last_login_at' => now(),
            ]);
        }

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        Session::forget([
            'customer_auth.pending_whatsapp',
            'customer_auth.pending_name',
            'customer_auth.otp_hash',
            'customer_auth.otp_expires_at',
            'customer_auth.otp_attempts',
            'url.intended'
        ]);

        return redirect()->route('booking.dashboard');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp' => ['required', 'string', 'min:9', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
            'cf-turnstile-response' => ['required', 'string', new \App\Rules\TurnstileRule()],
        ], [
            'cf-turnstile-response.required' => 'Verifikasi keamanan (Turnstile) wajib dicentang.',
            'whatsapp.min' => 'Nomor WhatsApp terlalu pendek.',
            'whatsapp.max' => 'Nomor WhatsApp terlalu panjang.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
        ]);

        $phone = $this->normalizePhoneDigits((string) $validated['whatsapp']);

        $instanceCode = $request->route('instance_code');
        $instance = Instance::where('instance_code', $instanceCode)->first();

        if (!$instance) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Instansi tidak ditemukan pada URL ini.',
            ]);
        }

        $customer = $this->findCustomerByPhone($phone, $instance->id);

        if (!$customer || !$customer->whatsapp_verified_at) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Nomor WhatsApp belum terdaftar di instansi ini. Silakan daftar terlebih dahulu.',
            ]);
        }

        // Peringatan Keamanan: Siapa pun yang mengetahui nomor WhatsApp pengguna lain
        // dapat langsung masuk ke akun mereka tanpa persetujuan (OTP/Password).
        $customer->update(['last_login_at' => now()]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->intended(route('booking.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('booking.register');
    }

    private function normalizePhoneDigits(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        // Canonical: selalu simpan/pakai format 62xxxxxxxxxxx
        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        // Umum: user mengetik tanpa awalan (contoh: 8123xxxx)
        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /**
     * @return array<int, string>
     */
    private function phoneVariants(string $phoneDigits): array
    {
        $phoneDigits = $this->normalizePhoneDigits($phoneDigits);

        if ($phoneDigits === '') {
            return [];
        }

        $variants = [$phoneDigits];

        if (str_starts_with($phoneDigits, '0')) {
            $variants[] = '62' . substr($phoneDigits, 1);
        }

        if (str_starts_with($phoneDigits, '62')) {
            $variants[] = '0' . substr($phoneDigits, 2);

            // Legacy/format input: 8123xxxx (tanpa 0/62)
            $withoutCountry = substr($phoneDigits, 2);
            if ($withoutCountry !== '') {
                $variants[] = $withoutCountry;
            }
        }

        return array_values(array_unique(array_filter($variants, static fn ($v) => $v !== '')));
    }

    private function extractPublicOtpSendErrorReason(string $rawMessage): string
    {
        $message = trim($rawMessage);

        if ($message === '') {
            return '';
        }

        // Contoh: "Gagal mengirim OTP WhatsApp via Wablas: Invalid phone"
        $pos = strrpos($message, ':');
        if ($pos !== false) {
            $message = trim(substr($message, $pos + 1));
        }

        // Jangan tampilkan pesan terlalu panjang di UI
        if (mb_strlen($message) > 120) {
            $message = mb_substr($message, 0, 120) . '...';
        }

        return $message;
    }

    private function findCustomerByPhone(string $phoneDigits, ?int $instanceId = null): ?Customer
    {
        $variants = $this->phoneVariants($phoneDigits);

        if ($variants === []) {
            return null;
        }

        $query = Customer::query()->whereIn('phone', $variants);
        if ($instanceId !== null) {
            $query->where('instance_id', $instanceId);
        }

        return $query->first();
    }
}
