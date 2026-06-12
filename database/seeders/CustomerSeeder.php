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
            // Seed 1 akun tester statis untuk kemudahan testing flow (Login/Register)
            Customer::create([
                'instance_id' => $instance->id,
                'name' => 'Tester ' . $instance->instance_name,
                'phone' => '6281234567890', // Nomor telepon statis untuk testing
                'whatsapp_verified_at' => now(),
                'last_login_at' => now(),
            ]);

            for ($i = 0; $i < 20; $i++) {
                Customer::create([
                    'instance_id' => $instance->id,
                    'name' => $faker->name(),
                    // Format nomor telepon valid Indonesia dengan awalan 628
                    'phone' => '628' . $faker->randomNumber(9, true),
                    'whatsapp_verified_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                    'last_login_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                ]);
            }
        }
    }
}
