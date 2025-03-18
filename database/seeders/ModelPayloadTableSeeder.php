<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ModelPayloadTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('model_payloads')->insert([
            [
            'id' => 1,
            'model_type' => 'prompt',
            'payload_template' => '{"prompt":"{prompt}"}',
            'file_type' => 'image',
            'created_at' => '2025-03-06 16:04:31',
            'updated_at' => '2025-03-06 16:04:31',
            ],
            [
            'id' => 2,
            'model_type' => 'train',
            'payload_template' => '{"loras":[{"path":"{train.diffusers_lora_file}","scale":1}],"prompt":"{prompt}","embeddings":[],"model_name":"{title}","enable_safety_checker":true}',
            'file_type' => 'image',
            'created_at' => '2025-03-05 18:12:29',
            'updated_at' => NULL,
            ],
            [
            'id' => 3,
            'model_type' => 'video',
            'payload_template' => '{"prompt":"{prompt}","image_url":"{image_url}"}',
            'file_type' => 'video',
            'created_at' => '2025-03-05 18:12:34',
            'updated_at' => NULL,
            ],
            [
            'id' => 4,
            'model_type' => 'upscale',
            'payload_template' => '{"video_url":"{video_url}"}',
            'file_type' => 'video',
            'created_at' => '2025-03-13 14:37:43',
            'updated_at' => NULL,
            ],
            [
            'id' => 5,
            'model_type' => 'audio',
            'payload_template' => '{"audio_url":"{audio_url}","video_url":"{video_url}"}',
            'file_type' => 'audio',
            'created_at' => '2025-03-05 18:12:37',
            'updated_at' => NULL,
            ],
            [
            'id' => 6,
            'model_type' => 'background',
            'payload_template' => '{"image_url":"{image_url}"}',
            'file_type' => 'image',
            'created_at' => NULL,
            'updated_at' => NULL,
            ],
        ]);
    }
}
