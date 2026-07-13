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
                'performance_standards' => [
                    'fast' => ['max' => 5],
                    'normal' => ['min' => 6, 'max' => 15],
                    'slow' => ['min' => 16]
                ],
            ],
            [
                'service_name' => 'Pelayanan Dokumen',
                'queue_prefix' => 'DOK',
                'description' => 'Layanan pengurusan dokumen',
                'performance_standards' => [
                    'fast' => ['max' => 10],
                    'normal' => ['min' => 11, 'max' => 20],
                    'slow' => ['min' => 21]
                ],
            ],
            [
                'service_name' => 'Pelayanan Konsultasi',
                'queue_prefix' => 'KON',
                'description' => 'Layanan konsultasi publik',
                'performance_standards' => [
                    'fast' => ['max' => 15],
                    'normal' => ['min' => 16, 'max' => 30],
                    'slow' => ['min' => 31]
                ],
            ],
            [
                'service_name' => 'Pembayaran Retribusi',
                'queue_prefix' => 'BAY',
                'description' => 'Layanan pembayaran retribusi',
                'performance_standards' => [
                    'fast' => ['max' => 5],
                    'normal' => ['min' => 6, 'max' => 10],
                    'slow' => ['min' => 11]
                ],
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
