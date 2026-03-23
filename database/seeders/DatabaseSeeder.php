<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InstanceSeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            ServiceCounterSeeder::class,
            CustomerSeeder::class,
            QueueSeeder::class,
            MediaContentSeeder::class,
            QrCodeSeeder::class,
        ]);
    }
}
