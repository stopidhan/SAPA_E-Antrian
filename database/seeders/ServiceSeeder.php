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
                'description' => 'Layanan administrasi umum',
                'performance_standards' => ['max_wait_time' => 15, 'max_service_time' => 30],
            ],
            [
                'service_name' => 'Pelayanan Dokumen',
                'queue_prefix' => 'DOK',
                'description' => 'Layanan pengurusan dokumen',
                'performance_standards' => ['max_wait_time' => 20, 'max_service_time' => 45],
            ],
            [
                'service_name' => 'Pelayanan Konsultasi',
                'queue_prefix' => 'KON',
                'description' => 'Layanan konsultasi publik',
                'performance_standards' => ['max_wait_time' => 10, 'max_service_time' => 60],
            ],
            [
                'service_name' => 'Pembayaran Retribusi',
                'queue_prefix' => 'BAY',
                'description' => 'Layanan pembayaran retribusi',
                'performance_standards' => ['max_wait_time' => 10, 'max_service_time' => 15],
            ]
        ];

        foreach ($instances as $instance) {
            foreach ($services as $service) {
                Service::create([
                    'instance_id' => $instance->id,
                    'service_name' => $service['service_name'],
                    'queue_prefix' => $service['queue_prefix'],
                    'description' => $service['description'],
                    'performance_standards' => $service['performance_standards'],
                    'is_active' => true
                ]);
            }
        }
    }
}
