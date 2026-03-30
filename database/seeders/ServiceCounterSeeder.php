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

            // Buat 3 counter per instance
            for ($i = 1; $i <= 3; $i++) {
                $randomService = $services->random();

                ServiceCounter::create([
                    'instance_id' => $instance->id,
                    'service_id' => $randomService->id,
                    'user_id' => $users->random()->id, // Assign operator secara random
                    'counter_number' => (string)$i,
                    'is_active' => true
                ]);
            }
        }
    }
}
