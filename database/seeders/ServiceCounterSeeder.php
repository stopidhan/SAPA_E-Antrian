<?php

namespace Database\Seeders;

use App\Models\ServiceCounter;
use App\Models\Instance;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceCounterSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            $services = $instance->services;

            // Buat 3 counter per instance
            for ($i = 1; $i <= 3; $i++) {
                // Setiap counter melayani random service
                $randomService = $services->random();

                ServiceCounter::create([
                    'instance_id' => $instance->id,
                    'service_id' => $randomService->id,
                    'counter_number' => (string)$i,
                    'is_active' => true
                ]);
            }
        }
    }
}
