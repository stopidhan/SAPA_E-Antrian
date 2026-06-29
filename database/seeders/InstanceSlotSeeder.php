<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\InstanceSlot;
use Illuminate\Database\Seeder;

class InstanceSlotSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            $slots = [
                ['start_time' => '08:00', 'end_time' => '10:00', 'capacity' => 10],
                ['start_time' => '10:00', 'end_time' => '12:00', 'capacity' => 10],
                ['start_time' => '13:00', 'end_time' => '15:00', 'capacity' => 10],
            ];

            foreach ($slots as $slot) {
                InstanceSlot::create([
                    'instance_id' => $instance->id,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'capacity' => $slot['capacity'],
                ]);
            }
        }
    }
}
