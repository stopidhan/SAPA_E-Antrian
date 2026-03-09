<?php

use App\Http\Controllers\BookingOnlineController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('booking.register');
});

// ==========================================
// Booking — Pendaftaran Antrean Online
// ==========================================
Route::get('/booking', [BookingOnlineController::class, 'halamanRegister'])->name('booking.register');
Route::post('/booking', [BookingOnlineController::class, 'prosesRegister'])->name('booking.register.submit');
Route::get('/booking/dashboard', [BookingOnlineController::class, 'halamanDashboard'])->name('booking.dashboard');
Route::post('/booking/ambil-antrean', [BookingOnlineController::class, 'prosesAmbilAntrean'])->name('booking.ambil-antrean');

Route::get('/booking/konfirmasi', function () {
    return view('booking.konfirmasi');
})->name('booking.konfirmasi');

Route::get('/booking/tiket', [BookingOnlineController::class, 'halamanTiket'])->name('booking.tiket');

Route::get('/booking/riwayat', function () {
    return view('booking.riwayat');
})->name('booking.riwayat');

Route::get('/booking/inventory', [BookingOnlineController::class, 'halamanInventory'])->name('booking.inventory');

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

Route::get('/profile-instance', function () {
    return view('Pages.AdminInstansi.profileInstance');
})->name('profile.instance');

Route::get('/report', function () {
    return view('Pages.AdminInstansi.report');
})->name('superadmin.report');

Route::get('/management-user', function () {
    return view('Pages.AdminInstansi.managementUser');
})->name('management.user');

Route::get('/activity-log', function () {
    return view('Pages.AdminInstansi.activityLog');
})->name('activity.log');

Route::get('/supervisor', function () {
    return view('Pages.KepalaLayanan.superVisor');
})->name('supervisor.dashboard');

Route::get('/content', function () {
    return view('Pages.StaffKonten.staffContent');
})->name('content.dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
