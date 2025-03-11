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
            'title' => 'Generate from Prompt',
            'model_name' => 'minimax-image',
            'model_type' => 'prompt',
            'base_url' => 'https://queue.fal.run/fal-ai/minimax-image',
            'payload' => '{"prompt":"{prompt}"}',
            'icon' => 'fa-pen',
            'enabled' => 0,
            'file_type' => 'image',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
            [
            'id' => 2,
            'title' => 'Generate from Model',
            'model_name' => 'flux-lora',
            'model_type' => 'train',
            'base_url' => 'https://queue.fal.run/fal-ai/flux-lora',
            'payload' => '{"loras":[{"path":"{train.diffusers_lora_file}","scale":1}],"prompt":"{prompt}","embeddings":[],"model_name":"{title}","enable_safety_checker":true}',
            'icon' => 'fa-user',
            'enabled' => 0,
            'file_type' => 'image',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
            [
            'id' => 3,
            'title' => 'Generate Videos',
            'model_name' => 'video-01-live',
            'model_type' => 'video',
            'base_url' => 'https://queue.fal.run/fal-ai/minimax/video-01-live/image-to-video',
            'payload' => '{"prompt":"{prompt}","image_url":"{image_url}"}',
            'icon' => 'fa-photo',
            'enabled' => 1,
            'file_type' => 'video',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
            [
            'id' => 4,
            'title' => 'Upscale Video',
            'model_name' => 'video-upscaler',
            'model_type' => 'upscale',
            'base_url' => 'https://queue.fal.run/fal-ai/video-upscaler',
            'payload' => '{"video_url":"{video_url}"}',
            'icon' => 'fa-video',
            'enabled' => 1,
            'file_type' => 'video',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
            [
            'id' => 5,
            'title' => 'Add Voice',
            'model_name' => 'sync-lipsync',
            'model_type' => 'audio',
            'base_url' => 'https://queue.fal.run/fal-ai/sync-lipsync',
            'payload' => '{"audio_url":"{audio_url}","video_url":"{video_url}"}',
            'icon' => 'fa-music',
            'enabled' => 1,
            'file_type' => 'audio',
            'created_at' => '2025-03-05 22:58:12',
            'updated_at' => NULL,
            ],
            [
            'id' => 16,
            'title' => 'Generate Image from Prompt',
            'model_name' => 'flux-lora',
            'model_type' => 'image',
            'base_url' => 'https://queue.fal.run/fal-ai/flux-lora',
            'payload' => '{"prompt":"prompt"}',
            'icon' => 'fa-pen',
            'enabled' => 0,
            'file_type' => 'image',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
            [
            'id' => 17,
            'title' => 'Remove Image Background',
            'model_name' => 'remove-background',
            'model_type' => 'background',
            'base_url' => 'https://queue.fal.run/fal-ai/bria/background/remove',
            'payload' => '{"image_url":"{image_url}"}',
            'icon' => 'fa-photo',
            'enabled' => 1,
            'file_type' => 'image',
            'created_at' => '2025-03-06 19:19:53',
            'updated_at' => NULL,
            ],
        ]);
    }
}