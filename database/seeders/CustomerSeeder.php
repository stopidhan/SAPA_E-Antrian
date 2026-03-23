<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Instance;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $instances = Instance::all();

        foreach ($instances as $instance) {
            for ($i = 0; $i < 20; $i++) {
                Customer::create([
                    'instance_id' => $instance->id,
                    'name' => $faker->name(),
                    'phone' => $faker->phoneNumber()
                ]);
            }
        }
    }
}
