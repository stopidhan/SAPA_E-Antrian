<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$instance_id = 1;

// 1. Antrean Pertama (Layanan A, Loket 1)
$q1 = \App\Models\Queue::with(['service', 'serviceCounter'])->create([
    'instance_id' => $instance_id,
    'customer_id' => null, 
    'service_id' => 1,
    'service_counter_id' => 1,
    'queue_number' => 'ADM-001',
    'queue_date' => now()->toDateString(),
    'taken_time' => now()->format('H:i:s'),
    'call_time' => now()->format('H:i:s'),
    'queue_status' => 'called',
    'queue_source' => 'onsite',
]);
$q1_full = \App\Models\Queue::with('serviceCounter')->find($q1->id);
$q1_full->counter_number = 'Loket 1';

// 2. Antrean Kedua (Layanan B, Loket 2)
$q2 = \App\Models\Queue::with(['service', 'serviceCounter'])->create([
    'instance_id' => $instance_id,
    'customer_id' => null, 
    'service_id' => 2,
    'service_counter_id' => 2,
    'queue_number' => 'MED-001',
    'queue_date' => now()->toDateString(),
    'taken_time' => now()->format('H:i:s'),
    'call_time' => now()->format('H:i:s'),
    'queue_status' => 'called',
    'queue_source' => 'onsite',
]);
$q2_full = \App\Models\Queue::with('serviceCounter')->find($q2->id);
$q2_full->counter_number = 'Loket 2';


// 3. Tembakkan dua event secara bersamaan (hanya beda hitungan milidetik)
event(new \App\Events\QueueUpdated('called', $q1_full, $instance_id));
event(new \App\Events\QueueUpdated('called', $q2_full, $instance_id));

echo "Dua event berhasil ditembakkan serentak!\n";
echo "1. Panggilan ADM-001 ke Loket 1\n";
echo "2. Panggilan MED-001 ke Loket 2\n";
echo "\nSilakan cek layar Public Monitor Anda! Seharusnya mereka mengantre untuk muncul (tidak bertabrakan suaranya).";
