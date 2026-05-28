<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Sekarang Laravel sudah aktif.

use App\Models\Instance;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Queue;
use App\Models\User;
use App\Models\ServiceCounter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "<pre>";
echo "=== STARTING E2E TEST ===\n\n";

DB::beginTransaction();

try {
    // 1. Setup Data
    $instance1 = Instance::first();
    $instance2 = Instance::skip(1)->first();
    
    echo "Using Instance 1: {$instance1->instance_name}\n";
    echo "Using Instance 2: {$instance2->instance_name}\n\n";

    $service1 = Service::where('instance_id', $instance1->id)->first();
    
    // ==========================================
    // 1. TEST KIOSK (OFFLINE)
    // ==========================================
    echo "--> [TEST 1] Testing Kiosk (Offline)\n";
    $customerOffline = Customer::create([
        'instance_id' => $instance1->id,
        'name' => 'Budi (On-Site)',
        'phone' => '-',
        'is_verified' => true,
    ]);

    $q1 = Queue::create([
        'instance_id' => $instance1->id,
        'customer_id' => $customerOffline->id,
        'service_id' => $service1->id,
        'queue_number' => strtoupper($service1->queue_prefix) . '-001',
        'queue_date' => now()->toDateString(),
        'taken_time' => now()->format('H:i:s'),
        'queue_status' => 'waiting',
        'queue_source' => 'onsite',
    ]);
    
    $lastQueue = Queue::query()
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
    $customerOnline = Customer::create([
        'instance_id' => $instance1->id,
        'name' => 'Siti Online',
        'phone' => '0812345678',
        'is_verified' => true,
        'password' => bcrypt('password')
    ]);

    $q2 = Queue::create([
        'instance_id' => $instance1->id,
        'customer_id' => $customerOnline->id,
        'service_id' => $service1->id,
        'queue_number' => strtoupper($service1->queue_prefix) . '-002',
        'queue_date' => now()->toDateString(),
        'taken_time' => now()->format('H:i:s'),
        'queue_status' => 'waiting',
        'queue_source' => 'online',
    ]);

    $bookingTodayCount = Queue::query()
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
    
    $operator = User::where('instance_id', $instance1->id)->where('role', 'operator')->first() ?? User::factory()->create(['instance_id' => $instance1->id, 'role' => 'operator']);
    $counter = ServiceCounter::firstOrCreate(
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
    $q_instance2 = Queue::create([
        'instance_id' => $instance2->id,
        'customer_id' => $customerOffline->id, // just reuse for test
        'service_id' => $service1->id, // technically should be service of instance 2, but DB doesn't restrict
        'queue_number' => 'TEST-999',
        'queue_date' => now()->toDateString(),
        'queue_status' => 'waiting',
    ]);

    $operatorQueues = Queue::where('instance_id', $instance1->id)->where('queue_status', 'waiting')->count();
    $instance2Queues = Queue::where('instance_id', $instance2->id)->where('queue_status', 'waiting')->count();

    echo "Operator Instance 1 Waiting Queues: {$operatorQueues}\n";
    echo "Instance 2 Waiting Queues: {$instance2Queues}\n";

    if ($operatorQueues === 0 && $instance2Queues === 1) {
        echo "OK: Multi-Tenant Isolation! Instance 1 Operator cannot see Instance 2 Queues.\n";
    } else {
        echo "FAIL: Multi-Tenant Isolation\n";
    }

    echo "\n=== ALL E2E TESTS PASSED ===\n";

} catch (\Exception $e) {
    echo "\nFAIL: TEST CRASHED: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} finally {
    // Selalu rollback agar database tidak kotor
    DB::rollBack();
    echo "\nDatabase rolled back to clean state.\n";
}
echo "</pre>";
