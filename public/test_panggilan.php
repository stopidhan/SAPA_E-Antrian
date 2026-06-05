<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$instance_id = 1;

// 1. Buat antrean bohongan dengan relasi lengkap
$q = \App\Models\Queue::with(['service', 'serviceCounter'])->create([
    'instance_id' => $instance_id,
    'customer_id' => null, 
    'service_id' => 1,
    'service_counter_id' => 1,
    'queue_number' => 'TES-001',
    'queue_date' => now()->toDateString(),
    'taken_time' => now()->format('H:i:s'),
    'call_time' => now()->format('H:i:s'),
    'queue_status' => 'called',
    'queue_source' => 'onsite',
]);

// Pastikan ngambil object utuh beserta join relasi counter_number
$q_full = \App\Models\Queue::with('serviceCounter')->find($q->id);

// Menambahkan atribut custom agar dibaca sama dengan respon API Monitor
$q_full->counter_number = $q_full->serviceCounter->counter_number ?? 'Loket 1';

// 2. Trigger Event Reverb dengan MENGIRIM OBJECT MODEL, bukan sekadar ID
event(new \App\Events\QueueUpdated('called', $q_full, $instance_id));

echo "Event 'Panggilan TES-001 ke Loket 1' berhasil ditembakkan ke WebSocket dengan object utuh!";
