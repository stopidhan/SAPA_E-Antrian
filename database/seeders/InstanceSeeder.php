<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstanceSeeder extends Seeder
{
    public function run(): void
    {
        $operationalHours = [
            ["name" => "Senin", "isOpen" => true, "openTime" => "08:00", "closeTime" => "16:00"],
            ["name" => "Selasa", "isOpen" => true, "openTime" => "08:00", "closeTime" => "16:00"],
            ["name" => "Rabu", "isOpen" => true, "openTime" => "08:00", "closeTime" => "16:00"],
            ["name" => "Kamis", "isOpen" => true, "openTime" => "08:00", "closeTime" => "16:00"],
            ["name" => "Jumat", "isOpen" => true, "openTime" => "08:00", "closeTime" => "16:00"],
            ["name" => "Sabtu", "isOpen" => false, "openTime" => "08:00", "closeTime" => "14:00"],
            ["name" => "Minggu", "isOpen" => false, "openTime" => "08:00", "closeTime" => "14:00"]
        ];

        Instance::create([
            'instance_code' => Str::uuid(),
            'instance_name' => 'Kantor Kecamatan Pusat',
            'address' => 'Jl. Merdeka No. 123, Kota',
            'phone' => '081234567890',
            'email' => 'kantor@kecamatan.go.id',
            'logo' => null,
            'favicon' => null,
            'tts_enabled' => true,
            'tts_language' => 'id-ID',
            'max_offline_bookings_per_day' => 100,
            'is_active' => true,
            'brand_color' => null,
            'secondary_color' => null,
            'latitude' => null,
            'longitude' => null,
            'operational_hours' => $operationalHours,
            'whatsapp_number' => null,
            'instagram' => null,
            'facebook' => null,
            'timezone' => 'Asia/Jakarta',
            'settings' => null,
        ]);

        Instance::create([
            'instance_code' => Str::uuid(),
            'instance_name' => 'Kantor Pelayanan Kesehatan',
            'address' => 'Jl. Kesehatan No. 456, Kota',
            'phone' => '081234567891',
            'email' => 'info@puskesmas.kota.go.id',
            'logo' => null,
            'favicon' => null,
            'tts_enabled' => false,
            'tts_language' => 'id-ID',
            'max_offline_bookings_per_day' => 100,
            'is_active' => true,
            'brand_color' => null,
            'secondary_color' => null,
            'latitude' => null,
            'longitude' => null,
            'operational_hours' => $operationalHours,
            'whatsapp_number' => null,
            'instagram' => null,
            'facebook' => null,
            'timezone' => 'Asia/Jakarta',
            'settings' => null,
        ]);
    }
}
