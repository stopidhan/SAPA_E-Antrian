<?php

use App\Http\Controllers\BookingOnlineController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileInstanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AdminInstanceController;
use App\Http\Controllers\MediaContentController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\SuperVisorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeveloperController;

// ==========================================
// Organization Selection (Root)
// ==========================================
Route::get('/', function () {
    $instances = \App\Models\Instance::all();
    return view('Pages.SelectInstance', compact('instances'));
})->name('select.instance');

// ==========================================
// Developer Dashboard (Superadmin)
// ==========================================
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('developer')->group(function () {
    Route::get('/instances', [DeveloperController::class, 'index'])->name('developer.instances.index');
    Route::get('/instances/create', [DeveloperController::class, 'create'])->name('developer.instances.create');
    Route::post('/instances', [DeveloperController::class, 'store'])->name('developer.instances.store');
    Route::get('/instances/{instance}/edit', [DeveloperController::class, 'edit'])->name('developer.instances.edit');
    Route::patch('/instances/{instance}', [DeveloperController::class, 'update'])->name('developer.instances.update');
    Route::post('/instances/{instance}/impersonate', [DeveloperController::class, 'impersonate'])->name('developer.instances.impersonate');
    Route::post('/stop-impersonating', [DeveloperController::class, 'stopImpersonating'])->name('developer.stop-impersonating');
});

// Fallback redirects
Route::prefix('booking')->group(function () {
    Route::redirect('/', '/');
});
Route::prefix('kiosk')->group(function () {
    Route::redirect('/', '/');
});
Route::prefix('monitor')->group(function () {
    Route::redirect('/', '/');
});
Route::prefix('staff')->group(function () {
    Route::redirect('/', '/');
});

// ==========================================
// Tenant Specific Routes
// ==========================================
Route::middleware([\App\Http\Middleware\IdentifyTenant::class, \App\Http\Middleware\CheckInstanceStatus::class])->prefix('{instance_slug}')->group(function () {

    Route::get('/', function (string $instanceSlug) {
        return redirect()->route('booking.register', ['instance_slug' => $instanceSlug]);
    });

    // ==========================================
    // Booking — Pendaftaran Antrean Online (Multi-Tenant)
    // ==========================================
    Route::prefix('booking')->group(function () {
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

    // ==========================================
    // Kiosk — Mesin Kiosk Layar Sentuh (On-Site)
    // ==========================================
    Route::prefix('kiosk')->group(function () {
        Route::get('/', [KioskController::class, 'halamanHome'])->name('kiosk.home');
        Route::get('/input', [KioskController::class, 'halamanInput'])->name('kiosk.input');
        Route::post('/input/simpan', [KioskController::class, 'simpanAntreanOffline'])->name('kiosk.input.simpan');
        Route::get('/cetak', [KioskController::class, 'halamanCetak'])->name('kiosk.cetak');
        Route::get('/scan', [KioskController::class, 'halamanScan'])->name('kiosk.scan');
        Route::post('/verify-scan', [KioskController::class, 'verifyScan'])->name('kiosk.verify-scan');
    });

    // ==========================================
    // Monitor — TV Ruang Tunggu (Public Display)
    // ==========================================
    Route::prefix('monitor')->group(function () {
        Route::get('/', [MonitorController::class, 'index'])->name('monitor.display');
        Route::get('/api', [MonitorController::class, 'getMonitorApi'])->name('monitor.api');
    });

    // ==========================================
    // Staff — Authenticated Management
    // ==========================================
    Route::prefix('staff')->group(function () {

        // Include Auth routes (login, password reset, etc) for staff
        require __DIR__ . "/auth.php";

        Route::middleware(['auth', 'verified'])->group(function () {

            Route::get('/dashboard', function () {
                return view('dashboard');
            })->name('dashboard');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            // Operator Dashboard
            Route::middleware(['role:staff_operator,admin_instansi'])->prefix('operator')->group(function () {
                Route::get('/', [OperatorController::class, 'index'])->name('operator.dashboard');
                Route::post('/open-session', [OperatorController::class, 'openSession'])->name('operator.session.open');
                Route::post('/close-session', [OperatorController::class, 'closeSession'])->name('operator.session.close');
                Route::get('/current-status', [OperatorController::class, 'currentStatus'])->name('operator.session.status');

                Route::post('/panggil/{id}', [OperatorController::class, 'panggilAntrean'])->name('operator.panggil');
                Route::post('/layani/{id}', [OperatorController::class, 'layaniAntrean'])->name('operator.layani');
                Route::post('/lewati/{id}', [OperatorController::class, 'lewatiAntrean'])->name('operator.lewati');
                // Route::post('/batal/{id}', [OperatorController::class, 'batalkanAntrean'])->name('operator.batal'); // Disembunyikan sementara
                Route::post('/selesai/{id}', [OperatorController::class, 'selesaiAntrean'])->name('operator.selesai');
                Route::get('/api/queues', [OperatorController::class, 'getQueuesApi'])->name('operator.api.queues');
            });

            // Supervisor Dashboard
            Route::middleware(['role:kepala_layanan,admin_instansi'])->prefix('supervisor')->group(function () {
                Route::get('/', [SuperVisorController::class, 'index'])->name('supervisor.dashboard');
                Route::get('/api/live', [SuperVisorController::class, 'liveApi'])->name('supervisor.api.live');
                Route::get('/api/live/partial', [SuperVisorController::class, 'livePartial'])->name('supervisor.api.live.partial');
                Route::get('/api/queue/{queue}', [SuperVisorController::class, 'queueDetail'])->name('supervisor.api.queue-detail');
                Route::get('/export/live/pdf', [SuperVisorController::class, 'exportLivePdf'])->name('supervisor.export.live.pdf');
                Route::get('/export/live/excel', [SuperVisorController::class, 'exportLiveExcel'])->name('supervisor.export.live.excel');
                Route::get('/export/history/pdf', [SuperVisorController::class, 'exportHistoryPdf'])->name('supervisor.export.history.pdf');
                Route::get('/export/history/excel', [SuperVisorController::class, 'exportHistoryExcel'])->name('supervisor.export.history.excel');
            });

            // Media Content Dashboard
            Route::middleware(['role:staff_konten,admin_instansi'])->prefix('content')->group(function () {
                Route::get('/', [MediaContentController::class, "index"])->name("content.index");
                Route::post('/', [MediaContentController::class, "store"])->name("content.store");
                Route::patch('/{content}', [MediaContentController::class, "update"])->name("content.update");
                Route::patch('/{content}/toggle', [MediaContentController::class, "toggle"])->name("content.toggle");
                Route::delete('/{content}', [MediaContentController::class, "destroy"])->name("content.destroy");
            });

            // Admin Dashboard
            Route::middleware(['role:admin_instansi'])->prefix('admin')->group(function () {
                Route::get('/', [AdminInstanceController::class, "index"])->name("admininstance.dashboard");

                // Services
                Route::get('/services', [AdminInstanceController::class, "getServices"])->name("services.index");
                Route::post('/services', [AdminInstanceController::class, "storeService"])->name("services.store");
                Route::patch('/services/{service}', [AdminInstanceController::class, "updateService"])->name("services.update");
                Route::delete('/services/{service}', [AdminInstanceController::class, "destroyService"])->name("services.destroy");
                Route::patch('/services/{service}/toggle', [AdminInstanceController::class, "toggleService"])->name("services.toggle");

                // Counters
                Route::delete('/counters/{counter}', [AdminInstanceController::class, "deleteCounter"])->name("counters.destroy");

                // Config
                Route::get('/instance-config', [AdminInstanceController::class, "getConfig"])->name("instance.config.show");
                Route::patch('/instance-config', [AdminInstanceController::class, "updateConfig"])->name("instance.config.update");

                // User Management
                Route::get('/management-user', [UserManagementController::class, "index"])->name("management.user");
                Route::post('/users', [UserManagementController::class, "store"])->name("users.store");
                Route::patch('/users/{user}', [UserManagementController::class, "update"])->name("users.update");
                Route::patch('/users/{user}/toggle', [UserManagementController::class, "toggleStatus"])->name("users.toggle");
                Route::post('/users/{user}/reset-password', [UserManagementController::class, "resetPassword"])->name("users.reset-password");
                Route::delete('/users/{user}', [UserManagementController::class, "destroy"])->name("users.destroy");

                Route::get('/profile-instance', [ProfileInstanceController::class, "edit"])->name("profile.instance");
                Route::patch('/profile-instance', [ProfileInstanceController::class, "update"])->name("profile.instance.update");

                // Reports
                Route::get('/report', [ReportController::class, "index"])->name("reports.index");
                Route::get('/report/api/queue/{queue}', [ReportController::class, "queueDetail"])->name("reports.api.queue-detail");
                Route::get('/report/export/pdf', [ReportController::class, "exportPdf"])->name("reports.export.pdf");
                Route::get('/report/export/excel', [ReportController::class, "exportExcel"])->name("reports.export.excel");

                Route::get("/activity-log", function () {
                    return view("Pages.AdminInstansi.activityLog");
                })->name("activity.log");
            });
        });
    });
});
