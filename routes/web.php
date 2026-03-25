<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileInstanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// Booking — Pendaftaran Antrean Online
// ==========================================
Route::get('/booking', function () {
    return view('booking.login');
})->name('booking.login');

Route::get('/booking/dashboard', function () {
    return view('booking.dashboard');
})->name('booking.dashboard');

Route::get('/booking/konfirmasi', function () {
    return view('booking.konfirmasi');
})->name('booking.konfirmasi');

Route::get('/booking/tiket', function () {
    return view('booking.tiket');
})->name('booking.tiket');

Route::get('/booking/riwayat', function () {
    return view('booking.riwayat');
})->name('booking.riwayat');

Route::get('/booking/inventory', function () {
    return view('booking.inventory');
})->name('booking.inventory');

// ==========================================
// Kiosk — Mesin Kiosk Layar Sentuh (On-Site)
// ==========================================
Route::get('/kiosk', function () {
    return view('kiosk.kiosk-home');
})->name('kiosk.home');

Route::get('/kiosk/input', function () {
    return view('kiosk.kiosk-input');
})->name('kiosk.input');

Route::get('/kiosk/cetak', function () {
    return view('kiosk.kiosk-cetak');
})->name('kiosk.cetak');

Route::get('/kiosk/scan', function () {
    return view('kiosk.kiosk-scan');
})->name('kiosk.scan');

// ==========================================
// Monitor — TV Ruang Tunggu (Public Display)
// ==========================================
Route::get('/monitor', function () {
    return view('monitor.monitor');
})->name('monitor.display');

// ==========================================
// Operator — Dashboard Operator Loket
// ==========================================
Route::get('/operator', function () {
    return view('operator.index');
})->name('operator.dashboard');


// ==========================================
Route::get('/superadmin', function () {
    return view('Pages.AdminInstansi.superAdmin');
})->name('superadmin.dashboard');

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
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
