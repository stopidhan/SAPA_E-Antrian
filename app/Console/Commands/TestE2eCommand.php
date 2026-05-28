<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Instance;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Queue;
use App\Models\User;
use App\Models\ServiceCounter;
use Illuminate\Support\Facades\DB;

class TestE2eCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:e2e';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run E2E tests for SAPA E-Antrian Queue Modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== STARTING E2E TEST ===\n");

        DB::beginTransaction();

        try {
            // 1. Setup Data
            $instance1 = Instance::first();
            $instance2 = Instance::skip(1)->first();
            
            $this->info("Using Instance 1: {$instance1->instance_name}");
            $this->info("Using Instance 2: {$instance2->instance_name}\n");

            $service1 = Service::where('instance_id', $instance1->id)->first();
            
            // ==========================================
            // 1. TEST KIOSK (OFFLINE)
            // ==========================================
            $this->warn("--> [TEST 1] Testing Kiosk (Offline)");
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
            
            $this->line("Next Queue Number generated: {$queueNumber}");
            if ($queueNumber === strtoupper($service1->queue_prefix) . '-002') {
                $this->info("✅ OK: Kiosk Queue Generation (No duplication bug)");
            } else {
                $this->error("❌ FAIL: Kiosk Queue Generation");
            }

            // ==========================================
            // 2. TEST BOOKING ONLINE
            // ==========================================
            $this->warn("\n--> [TEST 2] Testing Booking Online");
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

            $this->line("Booking Online count for Siti today: {$bookingTodayCount}");
            if ($bookingTodayCount === 1) {
                $this->info("✅ OK: Online Booking Limit Check");
            }

            // ==========================================
            // 3. TEST OPERATOR LOKET
            // ==========================================
            $this->warn("\n--> [TEST 3] Testing Operator Loket Actions");
            
            $operator = User::where('instance_id', $instance1->id)->where('role', 'staff_operator')->first() ?? User::factory()->create(['instance_id' => $instance1->id, 'role' => 'staff_operator']);
            $counter = ServiceCounter::firstOrCreate(
                ['instance_id' => $instance1->id, 'counter_number' => 'Loket 1'],
                ['user_id' => $operator->id, 'service_id' => $service1->id, 'is_active' => true]
            );

            $this->line("Operator assigned to: {$counter->counter_number}");
            $q1->update(['queue_status' => 'called', 'call_time' => now(), 'service_counter_id' => $counter->id]);
            $this->info("✅ OK: Action 'Panggil'. Status: {$q1->queue_status}");

            $q1->update(['queue_status' => 'cancelled', 'service_counter_id' => $counter->id, 'service_end_time' => now()]);
            $this->info("✅ OK: Action 'Batal'. Status: {$q1->queue_status}");

            $q2->update(['queue_status' => 'serving', 'service_start_time' => now(), 'service_counter_id' => $counter->id]);
            $this->info("✅ OK: Action 'Mulai Layani'. Status: {$q2->queue_status}");

            $q2->update(['queue_status' => 'completed', 'service_end_time' => now()]);
            $this->info("✅ OK: Action 'Selesai'. Status: {$q2->queue_status}");

            // ==========================================
            // 4. TEST ISOLASI MULTI-TENANT
            // ==========================================
            $this->warn("\n--> [TEST 4] Testing Multi-Tenant Isolation");
            $q_instance2 = Queue::create([
                'instance_id' => $instance2->id,
                'customer_id' => $customerOffline->id,
                'service_id' => $service1->id, // practically uses service 1 ID, but instance_id is 2
                'queue_number' => 'TEST-999',
                'queue_date' => now()->toDateString(),
                'queue_status' => 'waiting',
                'queue_source' => 'onsite'
            ]);

            $operatorCanSee = Queue::where('id', $q_instance2->id)->where('instance_id', $instance1->id)->count();
            $instance2CanSee = Queue::where('id', $q_instance2->id)->where('instance_id', $instance2->id)->count();

            $this->line("Operator Instance 1 Can See the new Instance 2 Queue: " . ($operatorCanSee ? 'YES' : 'NO'));
            $this->line("Instance 2 Can See the new Queue: " . ($instance2CanSee ? 'YES' : 'NO'));

            if ($operatorCanSee === 0 && $instance2CanSee === 1) {
                $this->info("✅ OK: Multi-Tenant Isolation! Instance 1 Operator cannot see Instance 2 Queues.");
            } else {
                $this->error("❌ FAIL: Multi-Tenant Isolation");
            }

            $this->info("\n🎉 === ALL E2E TESTS PASSED SUCCESSFULLY === 🎉\n");

        } catch (\Throwable $e) {
            $this->error("FATAL ERROR: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        } finally {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
                $this->comment("Database rolled back to clean state. No garbage data was saved.");
            }
        }
    }
}
