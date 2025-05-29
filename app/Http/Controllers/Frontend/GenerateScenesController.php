<?php

namespace App\Http\Controllers\Frontend;
ini_set('max_execution_time', 120); // Set to 120 seconds or adjust as needed

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Scene;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \GuzzleHttp\Client;

class GenerateScenesController extends Controller
{
    /**
     * Execute the job.
     */
    public function createScenes(Campaign $campaign)
    {
        // Dispatch the job to generate scenes for the campaign
        $this->generateScenes($campaign);
    }

    /**
     * Generate scenes for the campaign using OpenAI API.
     */
    public function generateScenes(Campaign $campaign): void
    {
        // Check if campaign has scenes already, do not generate new ones
        if ($campaign->scenes()->count() > 0) {
            return; // Exit if scenes already exist
        }

        try {
            // Use the HTTP client to make a request to the OpenAI API 
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4-0613',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a creative advertising scriptwriter. Generate ad scenes in structured JSON format. for each scene describe in consistent detail, the character description in each scene, do not use words like "the same person", or "same character", repeat the description as each is its own unique image generation prompt. Keep character_action to a minimal but impactful, and ensure the voice_over is engaging and relevant to the target audience. Since AI image generators do not do a good job generating hands and fingers, use less than 5 fingers in the character description, and avoid complex hand gestures. Use a variety of camera angles and zoom levels to create dynamic scenes. Ensure the language is consistent with the target audience, and use a mix of media types to keep the advertisement visually engaging.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $campaign->prompt,
                        ]
                    ],
                    'functions' => [
                        [
                            'name' => 'generate_ad_scenes',
                            'description' => 'Generate structured scenes for an advertisement.',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => [
                                    'scenes' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'title' => ['type' => 'string'],
                                                'media_type' => ['type' => 'string', 'enum' => ['video', 'image', 'text']],
                                                'scene_id' => ['type' => 'string'],
                                                'character_description' => ['type' => 'string'],
                                                'background_description' => ['type' => 'string'],
                                                'character_actions' => ['type' => 'string'],
                                                'voice_over' => ['type' => 'string'],
                                                'language' => ['type' => 'string'],
                                                'camera_angle' => ['type' => 'string'],
                                                'zoom_level' => ['type' => 'string']
                                            ],
                                            'required' => [
                                                'title',
                                                'media_type',
                                                'scene_id',
                                                'character_description',
                                                'background_description',
                                                'character_actions',
                                                'voice_over',
                                                'language',
                                                'camera_angle',
                                                'zoom_level'
                                            ]
                                        ]
                                    ]
                                ],
                                'required' => ['scenes']
                            ]
                        ]
                    ],
                    'function_call' => ['name' => 'generate_ad_scenes']
                ]
            ]);
           
            $data = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() !== 200) {
                Log::error('OpenAI API error', ['status_code' => $response->getStatusCode(), 'response' => $data]);
                $campaign->update(['status' => 'ERROR']);
                return;
            }

            // Log the response for debugging
            Log::info('OpenAI API response', ['response' => $data]);

            // Check if the response contains the expected data
            if (isset($data['choices'][0]['message']['function_call']['arguments'])) {
                $arguments = json_decode($data['choices'][0]['message']['function_call']['arguments'], true);
                Log::info('OpenAI API response', ['arguments' => $arguments]);

                // Update campaign status to processing
                $campaign->update(['status' => 'PROCESSING']);

                // Process each scene and save to the database
                foreach ($arguments['scenes'] as $scene) {
                    Scene::create(array_merge($scene, [
                        'campaign_id' => $campaign->id,
                        'user_id' => $campaign->user_id,
                        'status' => 'NEW'
                    ]));
                }

                // Update campaign status to completed
                $campaign->update(['status' => 'COMPLETED']);
            } else {
                Log::error('Unexpected response from OpenAI API', ['response' => $data]);
                $campaign->update(['status' => 'ERROR']);
            }
        } catch (\Exception $e) {
            Log::error('Error generating scenes', ['error' => $e->getMessage()]);
            $campaign->update(['status' => 'ERROR']);
        }
    }
} 