<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Instance;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        $services = [
            [
                'service_name' => 'Pelayanan Administrasi',
                'queue_prefix' => 'ADM',
                'description' => 'Layanan administrasi umum'
            ],
            [
                'service_name' => 'Pelayanan Dokumen',
                'queue_prefix' => 'DOK',
                'description' => 'Layanan pengurusan dokumen'
            ],
            [
                'service_name' => 'Pelayanan Konsultasi',
                'queue_prefix' => 'KON',
                'description' => 'Layanan konsultasi publik'
            ],
            [
                'service_name' => 'Pembayaran Retribusi',
                'queue_prefix' => 'BAY',
                'description' => 'Layanan pembayaran retribusi'
            ]
        ];

        foreach ($instances as $instance) {
            foreach ($services as $service) {
                Service::create([
                    'instance_id' => $instance->id,
                    'service_name' => $service['service_name'],
                    'queue_prefix' => $service['queue_prefix'],
                    'description' => $service['description'],
                    'is_active' => true
                ]);
            }
        }
    }
}
