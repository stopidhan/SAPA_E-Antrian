<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Instance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'instance_id' => null,
            'is_active' => true
        ]);

        // Users per instance
        foreach ($instances as $instance) {
            User::create([
                'name' => 'Admin ' . $instance->instance_name,
                'email' => 'admin' . $instance->id . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin_instansi',
                'instance_id' => $instance->id,
                'is_active' => true
            ]);

            User::create([
                'name' => 'Kepala Layanan ' . $instance->instance_name,
                'email' => 'kepala' . $instance->id . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'kepala_layanan',
                'instance_id' => $instance->id,
                'is_active' => true
            ]);

            for ($i = 1; $i <= 2; $i++) {
                User::create([
                    'name' => 'Operator ' . $i . ' ' . $instance->instance_name,
                    'email' => 'operator' . $instance->id . '_' . $i . '@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'staff_operator',
                    'instance_id' => $instance->id,
                    'is_active' => true
                ]);
            }

            User::create([
                'name' => 'Staff Konten ' . $instance->instance_name,
                'email' => 'konten' . $instance->id . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'staff_konten',
                'instance_id' => $instance->id,
                'is_active' => true
            ]);
        }
    }
}
