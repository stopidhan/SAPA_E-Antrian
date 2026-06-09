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
    return redirect('/demo/remoteuser'); // Redirect ke cabang default untuk percobaan
});




// ==========================================
// Booking — Pendaftaran Antrean Online (Multi-Tenant)
// ==========================================
Route::prefix('{instance_code}/remoteuser')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('booking.login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('booking.login.submit');

        Route::get('/', [CustomerAuthController::class, 'showRegisterForm'])->name('booking.register');
        Route::post('/send-otp', [CustomerAuthController::class, 'register'])->name('booking.register.submit');
        Route::get('/verifikasi-otp', [CustomerAuthController::class, 'showOtpForm'])->name('booking.otp.form');
        Route::post('/verifikasi-otp', [CustomerAuthController::class, 'verifyOtp'])->name('booking.otp.verify');
    });

    Route::middleware(['auth:customer', 'customer.instance'])->group(function () {
        Route::get('/dashboard', [BookingOnlineController::class, 'halamanDashboard'])->name('booking.dashboard');
        Route::post('/ambil-antrean', [BookingOnlineController::class, 'prosesAmbilAntrean'])->name('booking.ambil-antrean');

        Route::get('/konfirmasi', [BookingOnlineController::class, 'halamanKonfirmasi'])->name('booking.konfirmasi');

        Route::get('/tiket', [BookingOnlineController::class, 'halamanTiket'])->name('booking.tiket');
        Route::post('/tiket/set', [BookingOnlineController::class, 'setHalamanTiket'])->name('booking.tiket.set');
        Route::post('/tiket/hangus', [BookingOnlineController::class, 'tandaiTiketHangus'])->name('booking.tiket.expire');

        Route::get('/riwayat', [BookingOnlineController::class, 'halamanRiwayat'])->name('booking.riwayat');

        Route::get('/inventory', [BookingOnlineController::class, 'halamanInventory'])->name('booking.inventory');
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('booking.logout');
        Route::get('/logout', [CustomerAuthController::class, 'logout']);
    });
});

Route::prefix('booking')->group(function () {
    Route::redirect('/', '/demo/remoteuser');
    Route::redirect('/dashboard', '/demo/remoteuser/dashboard');
    Route::redirect('/konfirmasi', '/demo/remoteuser/konfirmasi');
    Route::redirect('/tiket', '/demo/remoteuser/tiket');
    Route::redirect('/riwayat', '/demo/remoteuser/riwayat');
    Route::redirect('/inventory', '/demo/remoteuser/inventory');
});

// ==========================================
// Kiosk — Mesin Kiosk Layar Sentuh (On-Site)
// ==========================================
use App\Http\Controllers\KioskController;

Route::prefix('{instance_code}')->group(function () {
    Route::get('/on-site-user', [KioskController::class, 'halamanHome'])->name('kiosk.home');
    Route::get('/on-site-user/input', [KioskController::class, 'halamanInput'])->name('kiosk.input');
    Route::post('/on-site-user/input/simpan', [KioskController::class, 'simpanAntreanOffline'])->name('kiosk.input.simpan');
    Route::get('/on-site-user/cetak', [KioskController::class, 'halamanCetak'])->name('kiosk.cetak');
    Route::get('/on-site-user/scan', [KioskController::class, 'halamanScan'])->name('kiosk.scan');
    Route::post('/on-site-user/verify-scan', [KioskController::class, 'verifyScan'])->name('kiosk.verify-scan');
});

Route::prefix('kiosk')->group(function () {
    Route::redirect('/', '/demo/on-site-user'); // Fallback ke demo
    Route::redirect('/input', '/demo/on-site-user/input');
    Route::redirect('/cetak', '/demo/on-site-user/cetak');
    Route::redirect('/scan', '/demo/on-site-user/scan');
});

// ==========================================
// Monitor — TV Ruang Tunggu (Public Display)
// ==========================================
use App\Http\Controllers\MonitorController;

Route::get('/{instance_code}/monitor', [MonitorController::class, 'index'])->name('monitor.display');
Route::get('/{instance_code}/monitor/api', [MonitorController::class, 'getMonitorApi'])->name('monitor.api');

// Jika monitor diakses tanpa instance_code as a fallback (opsional)
Route::redirect('/monitor', '/demo/monitor'); // default to 'demo' or whichever default instance

// ==========================================
// Operator — Dashboard Operator Loket
// ==========================================

Route::middleware(['auth', 'role:staff_operator'])->prefix('{instance_code}/staff-operator-loket')->group(function () {
    Route::get('/', [\App\Http\Controllers\OperatorController::class, 'index'])->name('operator.dashboard');
    Route::post('/panggil/{id}', [\App\Http\Controllers\OperatorController::class, 'panggilAntrean'])->name('operator.panggil');
    Route::post('/layani/{id}', [\App\Http\Controllers\OperatorController::class, 'layaniAntrean'])->name('operator.layani');
    Route::post('/lewati/{id}', [\App\Http\Controllers\OperatorController::class, 'lewatiAntrean'])->name('operator.lewati');
    Route::post('/batal/{id}', [\App\Http\Controllers\OperatorController::class, 'batalkanAntrean'])->name('operator.batal');
    Route::post('/selesai/{id}', [\App\Http\Controllers\OperatorController::class, 'selesaiAntrean'])->name('operator.selesai');
    Route::get('/api/queues', [\App\Http\Controllers\OperatorController::class, 'getQueuesApi'])->name('operator.api.queues');
});

// Redirect jika mengakses route lama ke route baru (opsional)
Route::middleware(['auth', 'role:staff_operator'])->get('/staff-operator-loket', function () {
    $instanceCode = auth()->user()->instance->instance_code ?? null;
    if (!$instanceCode) {
        abort(403, 'Anda tidak terdaftar di instansi manapun.');
    }
    return redirect()->route('operator.dashboard', ['instance_code' => $instanceCode]);
});
Route::redirect('/operator', '/staff-operator-loket');

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

// Rute khusus Admin Instansi (dilindungi role:admin_instansi)
Route::middleware(['auth', 'verified', 'role:admin_instansi'])->group(function () {
    Route::get('/superadmin', function () {
        return view('Pages.AdminInstansi.superAdmin');
    })->name('superadmin.dashboard');

    // Manajemen Layanan & Loket
    Route::resource('services', ServiceController::class);
    Route::delete('counters/{counter}', [ServiceController::class, 'deleteCounter'])->name('counters.destroy');
    Route::patch('services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

    // Manajemen Pengguna (User Management)
    Route::get('/management-user', [UserManagementController::class, 'index'])->name('management.user');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Profil & Laporan
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
