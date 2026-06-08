<?php

namespace Tests\Feature;

use App\Events\QueueUpdated;
use App\Models\Instance;
use App\Models\Queue;
use App\Models\Service;
use App\Models\ServiceCounter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OperatorMonitorFlowTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $instance;
    private $service;
    private $counter;
    private $queue;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Persiapkan Data Dasar: Instansi, User Operator, Loket, Layanan, dan Antrean
        $this->instance = Instance::create([
            'instance_code' => 'DEMO-01-CODE',
            'instance_slug' => 'DEMO-01',
            'instance_name' => 'Instansi Demo',
        ]);

        \Illuminate\Support\Facades\URL::defaults(['instance_slug' => $this->instance->instance_slug]);

        $this->user = User::factory()->create([
            'role' => 'staff_operator',
            'instance_id' => $this->instance->id,
        ]);

        $this->service = Service::create([
            'instance_id' => $this->instance->id,
            'service_name' => 'Layanan Demo',
            'service_code' => 'DM',
            'queue_prefix' => 'DM',
            'is_active' => true,
        ]);

        $this->counter = ServiceCounter::create([
            'instance_id' => $this->instance->id,
            'service_id' => $this->service->id,
            'user_id' => $this->user->id,
            'counter_number' => 1,
            'is_active' => true,
        ]);

        $this->queue = Queue::create([
            'instance_id' => $this->instance->id,
            'service_id' => $this->service->id,
            'queue_number' => 'DM-001',
            'queue_date' => now()->toDateString(),
            'queue_status' => 'waiting',
            'queue_source' => 'onsite'
        ]);
    }

    public function test_flow_panggilan_hingga_selesai_dan_update_monitor()
    {
        Event::fake([QueueUpdated::class]);

        // A. CEK STATUS AWAL MONITOR: Harus menampilkan loket "Menunggu"
        $monitorInit = $this->getJson(route('monitor.api', ['instance_slug' => $this->instance->instance_slug]));
        
        $monitorInit->assertStatus(200);
        $monitorDataInit = $monitorInit->json();
        $this->assertNull($monitorDataInit['current_call']);
        $this->assertEquals('Menunggu', $monitorDataInit['counters'][0]['status']);
        $this->assertEquals('-', $monitorDataInit['counters'][0]['queue_number']);

        // B. AKSI OPERATOR: Memanggil antrean
        $responsePanggil = $this->actingAs($this->user)->postJson(route('operator.panggil', $this->queue->id), [
            'counter_id' => $this->counter->id
        ]);
        
        $responsePanggil->assertStatus(200);
        $this->assertEquals('called', $this->queue->fresh()->queue_status);
        Event::assertDispatched(QueueUpdated::class, function ($event) {
            return $event->message === 'called';
        });

        // B.2. CEK MONITOR: Harus terpampang di antrean saat ini dan statusnya "Memanggil"
        $monitorPanggil = $this->getJson(route('monitor.api'));
        $monitorDataPanggil = $monitorPanggil->json();
        
        $this->assertEquals('DM-001', $monitorDataPanggil['current_call']['queue_number']);
        $this->assertEquals('Memanggil', $monitorDataPanggil['counters'][0]['status']);
        $this->assertEquals('DM-001', $monitorDataPanggil['counters'][0]['queue_number']);

        // C. AKSI OPERATOR: Melayani antrean
        $responseLayani = $this->actingAs($this->user)->postJson(route('operator.layani', ['instance_slug' => $this->instance->instance_slug, 'id' => $this->queue->id]), [
            'counter_id' => $this->counter->id
        ]);
        
        $responseLayani->assertStatus(200);
        $this->assertEquals('serving', $this->queue->fresh()->queue_status);
        Event::assertDispatched(QueueUpdated::class, function ($event) {
            return $event->message === 'serving';
        });

        // C.2. CEK MONITOR: Status loket menjadi "Dilayani"
        $monitorLayani = $this->getJson(route('monitor.api'));
        $monitorDataLayani = $monitorLayani->json();
        
        $this->assertEquals('Dilayani', $monitorDataLayani['counters'][0]['status']);

        // D. AKSI OPERATOR: Menyelesaikan antrean
        $responseSelesai = $this->actingAs($this->user)->postJson(route('operator.selesai', $this->queue->id), [
            'counter_id' => $this->counter->id,
            'category' => 'Umum',
            'description' => 'Selesai dilayani'
        ]);
        
        $responseSelesai->assertStatus(200);
        $this->assertEquals('completed', $this->queue->fresh()->queue_status);
        Event::assertDispatched(QueueUpdated::class, function ($event) {
            return $event->message === 'completed';
        });

        // D.2. CEK MONITOR: Antrean hilang dari current_call dan status kembali "Menunggu"
        $monitorSelesai = $this->getJson(route('monitor.api'));
        $monitorDataSelesai = $monitorSelesai->json();
        
        $this->assertNull($monitorDataSelesai['current_call']);
        $this->assertEquals('Menunggu', $monitorDataSelesai['counters'][0]['status']);
        $this->assertEquals('-', $monitorDataSelesai['counters'][0]['queue_number']);
    }
}