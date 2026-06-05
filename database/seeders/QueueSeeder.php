<?php

namespace Database\Seeders;

use App\Models\Queue;
use App\Models\Instance;
use App\Models\Service;
use App\Models\ServiceCounter;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class QueueSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $instances = Instance::all();

        $statuses = ['waiting', 'called', 'serving', 'completed', 'skipped'];
        $sources = ['onsite', 'online'];

        foreach ($instances as $instance) {
            $services = $instance->services;
            $counters = $instance->serviceCounters;
            $customers = $instance->customers;

            for ($i = 0; $i < 30; $i++) {
                $service = $services->random();

                $queueDate = Carbon::parse($faker->dateTimeBetween('-7 days')->format('Y-m-d'));
                $takenTime = (clone $queueDate)->setTime(rand(8, 15), rand(0, 59));
                $callTime = (clone $takenTime)->addMinutes(rand(2, 10));
                $serviceStartTime = (clone $callTime)->addMinutes(rand(1, 5));
                $serviceDuration = rand(5, 60);
                $serviceEndTime = (clone $serviceStartTime)->addMinutes($serviceDuration);

                Queue::create([
                    'instance_id' => $instance->id,
                    'service_counter_id' => $counters->random()->id,
                    'customer_id' => $customers->random()->id,
                    'service_id' => $service->id,
                    'queue_number' => $service->queue_prefix . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT),
                    'queue_date' => $queueDate->format('Y-m-d'),
                    'taken_time' => $takenTime->format('H:i:s'),
                    'call_time' => $callTime->format('H:i:s'),
                    'service_start_time' => $serviceStartTime->format('H:i:s'),
                    'service_end_time' => $serviceEndTime->format('H:i:s'),
                    'service_duration' => $serviceDuration,
                    'service_description' => $faker->sentence(),
                    'queue_status' => $faker->randomElement($statuses),
                    'queue_source' => $faker->randomElement($sources)
                ]);
            }
        }
    }
}
