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

                // Ambil operator secara berurutan, bukan random.
                // Jika loket ke-3 (karena cuma ada 2 operator), assign ke null atau user pertama lagi
                $assignedUser = $users->get($i - 1);

                ServiceCounter::create([
                    'instance_id' => $instance->id,
                    'service_id' => $randomService->id,
                    'user_id' => $assignedUser ? $assignedUser->id : null, // Assign operator secara pasti (tidak di-random)
                    'counter_number' => (string)$i,
                    'is_active' => true
                ]);
            }
        }
    }
}
