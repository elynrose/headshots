<?php

return [
    /*
    |--------------------------------------------------------------------------
    | fal.ai Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your fal.ai settings. You can find your API key
    | in your fal.ai dashboard.
    |
    */

    'api_key' => env('FAL_API_KEY'),
    
    'api_url' => env('FAL_API_URL', 'https://api.fal.ai'),
    
    'webhook_url' => env('FAL_WEBHOOK_URL'),
    
    'default_model' => env('FAL_DEFAULT_MODEL', 'stable-diffusion-v1-5'),
    
    'queue' => [
        'connection' => env('FAL_QUEUE_CONNECTION', 'redis'),
        'queue' => env('FAL_QUEUE', 'high'),
    ],
    
    'timeout' => env('FAL_TIMEOUT', 300),
    
    'retry_attempts' => env('FAL_RETRY_ATTEMPTS', 3),
    
    'models' => [
        'stable-diffusion-v1-5' => [
            'name' => 'Stable Diffusion v1.5',
            'type' => 'image',
            'endpoint' => '/v1/models/stable-diffusion-v1-5',
        ],
        'stable-diffusion-xl' => [
            'name' => 'Stable Diffusion XL',
            'type' => 'image',
            'endpoint' => '/v1/models/stable-diffusion-xl',
        ],
        'video-diffusion' => [
            'name' => 'Video Diffusion',
            'type' => 'video',
            'endpoint' => '/v1/models/video-diffusion',
        ],
    ],
]; 