<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ModelPayloadTableSeeder extends Seeder
{
    public function run()
    {
        $payloads = [
            [
                'model_type' => 'prompt',
                'payload_template' => '{"prompt":"{prompt}"}',
                'file_type' => 'image',
            ],
            [
                'model_type' => 'train',
                'payload_template' => '{"loras":[{"path":"{train.diffusers_lora_file}","scale":1}],"prompt":"{prompt}","embeddings":[],"model_name":"{title}","enable_safety_checker":true}',
                'file_type' => 'image',
            ],
            [
                'model_type' => 'video',
                'payload_template' => '{"prompt":"{prompt}","image_url":"{image_url}"}',
                'file_type' => 'video',
            ],
            [
                'model_type' => 'upscale',
                'payload_template' => '{"video_url":"{video_url}"}',
                'file_type' => 'video',
            ],
            [
                'model_type' => 'audio',
                'payload_template' => '{"audio_url":"{audio_url}","video_url":"{video_url}"}',
                'file_type' => 'audio',
            ],
            [
                'model_type' => 'background',
                'payload_template' => '{"image_url":"{image_url}"}',
                'file_type' => 'image',
            ],
            [
                'model_type' => 'image',
                'payload_template' => '{"prompt":"{prompt}"}',
                'file_type' => 'image',
            ],
        ];

        foreach ($payloads as $payload) {
            DB::table('model_payloads')->updateOrInsert(
                ['model_type' => $payload['model_type']],
                array_merge($payload, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
