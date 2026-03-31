<?php

use App\Http\Controllers\BookingOnlineController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileInstanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('booking.register');
});

// ==========================================
// Booking — Pendaftaran Antrean Online
// ==========================================
Route::middleware('guest:customer')->group(function () {
    Route::get('/remoteuser/login', [CustomerAuthController::class, 'showLoginForm'])->name('booking.login');
    Route::post('/remoteuser/login', [CustomerAuthController::class, 'login'])->name('booking.login.submit');

    Route::get('/remoteuser', [CustomerAuthController::class, 'showRegisterForm'])->name('booking.register');
    Route::post('/remoteuser/send-otp', [CustomerAuthController::class, 'register'])->name('booking.register.submit');
    Route::get('/remoteuser/verifikasi-otp', [CustomerAuthController::class, 'showOtpForm'])->name('booking.otp.form');
    Route::post('/remoteuser/verifikasi-otp', [CustomerAuthController::class, 'verifyOtp'])->name('booking.otp.verify');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/remoteuser/dashboard', [BookingOnlineController::class, 'halamanDashboard'])->name('booking.dashboard');
    Route::post('/remoteuser/ambil-antrean', [BookingOnlineController::class, 'prosesAmbilAntrean'])->name('booking.ambil-antrean');

    Route::get('/remoteuser/konfirmasi', [BookingOnlineController::class, 'halamanKonfirmasi'])->name('booking.konfirmasi');

    Route::get('/remoteuser/tiket', [BookingOnlineController::class, 'halamanTiket'])->name('booking.tiket');
    Route::post('/remoteuser/tiket/hangus', [BookingOnlineController::class, 'tandaiTiketHangus'])->name('booking.tiket.expire');

    Route::get('/remoteuser/riwayat', function () {
        return view('Pages.Remoteuser.Riwayat');
    })->name('booking.riwayat');

    Route::get('/remoteuser/inventory', [BookingOnlineController::class, 'halamanInventory'])->name('booking.inventory');
    Route::post('/remoteuser/logout', [CustomerAuthController::class, 'logout'])->name('booking.logout');
});

Route::prefix('booking')->group(function () {
    Route::redirect('/', '/remoteuser');
    Route::redirect('/dashboard', '/remoteuser/dashboard');
    Route::redirect('/konfirmasi', '/remoteuser/konfirmasi');
    Route::redirect('/tiket', '/remoteuser/tiket');
    Route::redirect('/riwayat', '/remoteuser/riwayat');
    Route::redirect('/inventory', '/remoteuser/inventory');
});

// ==========================================
// Kiosk — Mesin Kiosk Layar Sentuh (On-Site)
// ==========================================
use App\Http\Controllers\KioskController;

Route::get('/on-site-user', [KioskController::class, 'halamanHome'])->name('kiosk.home');
Route::get('/on-site-user/input', [KioskController::class, 'halamanInput'])->name('kiosk.input');
Route::get('/on-site-user/cetak', [KioskController::class, 'halamanCetak'])->name('kiosk.cetak');
Route::get('/on-site-user/scan', [KioskController::class, 'halamanScan'])->name('kiosk.scan');

// AJAX: Verifikasi Scan QR
Route::post('/on-site-user/verify-scan', [KioskController::class, 'verifyScan'])->name('kiosk.verify-scan');

Route::prefix('kiosk')->group(function () {
    Route::redirect('/', '/on-site-user');
    Route::redirect('/input', '/on-site-user/input');
    Route::redirect('/cetak', '/on-site-user/cetak');
    Route::redirect('/scan', '/on-site-user/scan');
});

// ==========================================
// Monitor — TV Ruang Tunggu (Public Display)
// ==========================================
Route::get('/monitor', function () {
    return view('Pages.MonitorPublic.monitor');
})->name('monitor.display');

// ==========================================
// Operator — Dashboard Operator Loket
// ==========================================
Route::middleware(['role:operator'])->group(function () {
    Route::get('/staff-operator-loket', function () {
        return view('Pages.StaffOperatorLoket.Index');
    })->name('operator.dashboard');
    Route::redirect('/operator', '/staff-operator-loket');
});

// ==========================================

Route::get('/report', function () {
    return view('Pages.AdminInstansi.report');
})->name('superadmin.report');

Route::get('/activity-log', function () {
    return view('Pages.AdminInstansi.activityLog');
})->name('activity.log');

Route::get('/supervisor', function () {
    return view('Pages.KepalaLayanan.superVisor');
})->name('supervisor.dashboard');

Route::get('/content', function () {
    return view('Pages.StaffKonten.staffContent');
})->name('content.dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/superadmin', function () {
        return view('Pages.AdminInstansi.superAdmin');
    })->name('superadmin.dashboard');

    Route::resource('services', ServiceController::class);
    Route::delete('counters/{counter}', [ServiceController::class, 'deleteCounter'])->name('counters.destroy');
    Route::patch('services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

    Route::get('/management-user', [UserManagementController::class, 'index'])->name('management.user');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile-instance', [ProfileInstanceController::class, 'edit'])->name('profile.instance');
    Route::patch('/profile-instance', [ProfileInstanceController::class, 'update'])->name('profile.instance.update');

    Route::get('/report', [ReportController::class, 'index'])->name('superadmin.report');


});

// --- TESTES ---

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
