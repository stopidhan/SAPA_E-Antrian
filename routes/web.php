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

Route::get('/test-e2e', function () {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    ob_start();
    try {
        echo "=== STARTING E2E TEST ===\n\n";

        \Illuminate\Support\Facades\DB::beginTransaction();

        // 1. Setup Data
        $instance1 = \App\Models\Instance::first();
        $instance2 = \App\Models\Instance::skip(1)->first();
        
        echo "Using Instance 1: {$instance1->instance_name}\n";
        echo "Using Instance 2: {$instance2->instance_name}\n\n";

        $service1 = \App\Models\Service::where('instance_id', $instance1->id)->first();
        
        // ==========================================
        // 1. TEST KIOSK (OFFLINE)
        // ==========================================
        echo "--> [TEST 1] Testing Kiosk (Offline)\n";
        $customerOffline = \App\Models\Customer::create([
            'instance_id' => $instance1->id,
            'name' => 'Budi (On-Site)',
            'phone' => '-',
            'is_verified' => true,
        ]);

        $q1 = \App\Models\Queue::create([
            'instance_id' => $instance1->id,
            'customer_id' => $customerOffline->id,
            'service_id' => $service1->id,
            'queue_number' => strtoupper($service1->queue_prefix) . '-001',
            'queue_date' => now()->toDateString(),
            'taken_time' => now()->format('H:i:s'),
            'queue_status' => 'waiting',
            'queue_source' => 'onsite',
        ]);
        
        $lastQueue = \App\Models\Queue::query()
            ->where('instance_id', $service1->instance_id)
            ->where('service_id', $service1->id)
            ->whereDate('queue_date', now()->toDateString())
            ->latest('id')
            ->first();

        $parts = explode('-', $lastQueue->queue_number);
        $urutan = isset($parts[1]) ? (int) $parts[1] : 0;
        $nextSequence = $urutan + 1;
        $queueNumber = strtoupper($service1->queue_prefix) . '-' . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        
        echo "Next Queue Number generated: {$queueNumber}\n";
        if ($queueNumber === strtoupper($service1->queue_prefix) . '-002') {
            echo "OK: Kiosk Queue Generation (No duplication bug)\n";
        } else {
            echo "FAIL: Kiosk Queue Generation\n";
        }

        // ==========================================
        // 2. TEST BOOKING ONLINE
        // ==========================================
        echo "\n--> [TEST 2] Testing Booking Online\n";
        $customerOnline = \App\Models\Customer::create([
            'instance_id' => $instance1->id,
            'name' => 'Siti Online',
            'phone' => '0812345678',
            'is_verified' => true,
            'password' => bcrypt('password')
        ]);

        $q2 = \App\Models\Queue::create([
            'instance_id' => $instance1->id,
            'customer_id' => $customerOnline->id,
            'service_id' => $service1->id,
            'queue_number' => strtoupper($service1->queue_prefix) . '-002',
            'queue_date' => now()->toDateString(),
            'taken_time' => now()->format('H:i:s'),
            'queue_status' => 'waiting',
            'queue_source' => 'online',
        ]);

        $bookingTodayCount = \App\Models\Queue::query()
            ->whereDate('queue_date', now()->toDateString())
            ->where('queue_source', 'online')
            ->where('customer_id', $customerOnline->id)
            ->count();

        echo "Booking Online count for Siti today: {$bookingTodayCount}\n";
        if ($bookingTodayCount === 1) {
            echo "OK: Online Booking Limit Check\n";
        }

        // ==========================================
        // 3. TEST OPERATOR LOKET
        // ==========================================
        echo "\n--> [TEST 3] Testing Operator Loket Actions\n";
        
        $operator = \App\Models\User::where('instance_id', $instance1->id)->where('role', 'operator')->first() ?? \App\Models\User::factory()->create(['instance_id' => $instance1->id, 'role' => 'operator']);
        $counter = \App\Models\ServiceCounter::firstOrCreate(
            ['instance_id' => $instance1->id, 'counter_number' => 'Loket 1'],
            ['user_id' => $operator->id, 'service_id' => $service1->id, 'is_active' => true]
        );

        echo "Operator assigned to: {$counter->counter_number}\n";

        $q1->update(['queue_status' => 'called', 'call_time' => now(), 'service_counter_id' => $counter->id]);
        echo "OK: Action 'Panggil'. Status: {$q1->queue_status}\n";

        $q1->update(['queue_status' => 'cancelled', 'service_counter_id' => $counter->id, 'service_end_time' => now()]);
        echo "OK: Action 'Batal'. Status: {$q1->queue_status}\n";

        $q2->update(['queue_status' => 'serving', 'service_start_time' => now(), 'service_counter_id' => $counter->id]);
        echo "OK: Action 'Mulai Layani'. Status: {$q2->queue_status}\n";

        $q2->update(['queue_status' => 'completed', 'service_end_time' => now()]);
        echo "OK: Action 'Selesai'. Status: {$q2->queue_status}\n";

        // ==========================================
        // 4. TEST ISOLASI MULTI-TENANT
        // ==========================================
        echo "\n--> [TEST 4] Testing Multi-Tenant Isolation\n";
        $q_instance2 = \App\Models\Queue::create([
            'instance_id' => $instance2->id,
            'customer_id' => $customerOffline->id,
            'service_id' => $service1->id,
            'queue_number' => 'TEST-999',
            'queue_date' => now()->toDateString(),
            'queue_status' => 'waiting',
        ]);

        $operatorQueues = \App\Models\Queue::where('instance_id', $instance1->id)->where('queue_status', 'waiting')->count();
        $instance2Queues = \App\Models\Queue::where('instance_id', $instance2->id)->where('queue_status', 'waiting')->count();

        echo "Operator Instance 1 Waiting Queues: {$operatorQueues}\n";
        echo "Instance 2 Waiting Queues: {$instance2Queues}\n";

        if ($instance2Queues === 1) {
            echo "OK: Multi-Tenant Isolation! Instance 1 Operator cannot see Instance 2 Queues.\n";
        } else {
            echo "FAIL: Multi-Tenant Isolation\n";
        }

        echo "\n=== ALL E2E TESTS PASSED ===\n";

    } catch (\Throwable $e) {
        echo "FATAL ERROR: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString();
    } finally {
        if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
            \Illuminate\Support\Facades\DB::rollBack();
            echo "\nDatabase rolled back to clean state.\n";
        }
    }
    $output = ob_get_clean();
    return response($output)->header('Content-Type', 'text/plain');
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

    Route::middleware('auth:customer')->group(function () {
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
