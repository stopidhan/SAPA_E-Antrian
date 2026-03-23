<?php

namespace Database\Seeders;

use App\Models\MediaContent;
use App\Models\Instance;
use Illuminate\Database\Seeder;

class MediaContentSeeder extends Seeder
{
    public function run(): void
    {
        $instances = Instance::all();

        foreach ($instances as $instance) {
            MediaContent::create([
                'instance_id' => $instance->id,
                'title' => 'Welcome Video',
                'media_type' => 'video',
                'file_path' => 'media/welcome.mp4',
                'duration' => 30,
                'is_active' => true
            ]);

            MediaContent::create([
                'instance_id' => $instance->id,
                'title' => 'Information Slide',
                'media_type' => 'image',
                'file_path' => 'media/info.jpg',
                'duration' => null,
                'is_active' => true
            ]);
        }
    }
}
