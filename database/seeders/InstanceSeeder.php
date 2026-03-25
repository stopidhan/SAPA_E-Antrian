<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstanceSeeder extends Seeder
{
    public function run(): void
    {
        Instance::create([
            'instance_code' => Str::uuid(),
            'instance_name' => 'Kantor Kecamatan Pusat',
            'address' => 'Jl. Merdeka No. 123, Kota',
            'phone' => '081234567890',
            'email' => 'kantor@kecamatan.go.id',
            'website' => 'https://kecamatan.go.id',
            'logo' => null
        ]);

        Instance::create([
            'instance_code' => Str::uuid(),
            'instance_name' => 'Kantor Pelayanan Kesehatan',
            'address' => 'Jl. Kesehatan No. 456, Kota',
            'phone' => '081234567891',
            'email' => 'puskesmas@kota.go.id',
            'website' => 'https://puskesmas.kota.go.id',
            'logo' => null
        ]);
    }
}
