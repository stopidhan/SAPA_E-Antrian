<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Queue::whereIn('queue_status', ['called', 'serving'])
    ->update(['queue_status' => 'completed']);

echo "All active queues have been marked as completed.";
