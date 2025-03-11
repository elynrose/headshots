<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FalTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fal')->insert([
            [
                'id' => 1,
                'title' => 'Audio',
                'model_name' => 'sync-lipsync',
                'model_type' => 'audio',
                'base_url' => 'https://queue.fal.run/fal-ai/sync-lipsync',
                'payload' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 2,
                'title' => 'Generate Photos',
                'model_name' => 'flux-lora',
                'model_type' => 'image',
                'base_url' => 'https://queue.fal.run/fal-ai/flux-lora',
                'payload' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 3,
                'title' => 'Generate Videos',
                'model_name' => 'video-01-live',
                'model_type' => 'video',
                'base_url' => 'https://queue.fal.run/fal-ai/minimax/video-01-live/image-to-video',
                'payload' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 4,
                'title' => 'Upscale Video',
                'model_name' => 'video-upscaler',
                'model_type' => 'upscale',
                'base_url' => 'https://queue.fal.run/fal-ai/video-upscaler',
                'payload' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
        ]);
    }
}