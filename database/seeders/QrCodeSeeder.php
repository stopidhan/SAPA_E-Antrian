<?php

namespace Database\Seeders;

use App\Models\QrCode;
use App\Models\Instance;
use Illuminate\Database\Seeder;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            QrCode::create([
                'instance_id' => $instance->id,
                'qr_data' => json_encode([
                    'instance_id' => $instance->id,
                    'instance_code' => $instance->instance_code,
                    'instance_name' => $instance->instance_name
                ])
            ]);
        }
    }
}
