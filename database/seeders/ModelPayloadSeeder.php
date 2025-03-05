<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelPayloadSeeder extends Seeder
{
    public function run()
    {
        DB::table('model_payloads')->insert([
            [
                'model_type' => 'image',
                'payload_template' => json_encode([
                    'loras' => [['path' => '{train.diffusers_lora_file}', 'scale' => 1]],
                    'prompt' => '{prompt}',
                    'embeddings' => [],
                    'model_name' => '{title}',
                    'enable_safety_checker' => true,
                ]),
            ],
            [
                'model_type' => 'video',
                'payload_template' => json_encode([
                    'prompt' => '{prompt}',
                    'image_url' => '{image_url}',
                ]),
            ],
            [
                'model_type' => 'audio',
                'payload_template' => json_encode([
                    'audio_url' => '{audio_url}',
                    'video_url' => '{video_url}',
                ]),
            ],
        ]);
    }
}
