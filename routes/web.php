<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
