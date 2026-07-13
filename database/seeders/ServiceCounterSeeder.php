<?php

namespace Database\Seeders;

use App\Models\ServiceCounter;
use App\Models\Instance;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceCounterSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            $services = $instance->services;
            // Ambil operator di instance ini
            $users = User::where('instance_id', $instance->id)
                ->where('role', 'staff_operator')
                ->get();

            // Jika tidak ada operator, skip atau gunakan semua user
            if ($users->isEmpty()) {
                $users = User::where('instance_id', $instance->id)->get();
            }

            $i = 1;
            foreach ($services as $service) {
                // Ambil operator secara berurutan, jika habis gunakan modulus
                $assignedUser = $users->count() > 0 ? $users->get(($i - 1) % $users->count()) : null;

                $counter = ServiceCounter::create([
                    'instance_id' => $service->instance_id,
                    'service_id' => $service->id,
                    'counter_number' => 'Loket ' . $i . ($service->queue_prefix ? ' ' . strtoupper($service->queue_prefix) : ''),
                    'is_active' => true,
                ]);

                if ($assignedUser) {
                    \App\Models\CounterSession::create([
                        'instance_id' => $service->instance_id,
                        'service_counter_id' => $counter->id,
                        'user_id' => $assignedUser->id,
                        'status' => 'closed',
                        'started_at' => null,
                        'ended_at' => now(),
                    ]);
                }

                $i++;
            }
        }
    }
}
