<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$queues = \App\Models\Queue::whereIn('queue_status', ['called', 'serving'])->get();

echo "Active Queues:\n";
foreach ($queues as $q) {
    echo "- ID: {$q->id}, Queue: {$q->queue_number}, Status: {$q->queue_status}, Counter: {$q->service_counter_id}\n";
}
