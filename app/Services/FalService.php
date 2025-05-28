<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessFalGeneration;

class FalService
{
    protected $apiKey;
    protected $apiUrl;
    protected $webhookUrl;

    public function __construct($apiKey, $apiUrl, $webhookUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->webhookUrl = $webhookUrl;
    }

    public function generateImage($prompt, $model = null, $options = [])
    {
        $model = $model ?? config('fal.default_model');
        $modelConfig = config("fal.models.{$model}");

        if (!$modelConfig) {
            throw new \Exception("Model {$model} not found in configuration");
        }

        $payload = array_merge([
            'prompt' => $prompt,
            'model' => $model,
            'webhook_url' => $this->webhookUrl,
        ], $options);

        try {
            $response = Http::fal()
                ->post($modelConfig['endpoint'], $payload)
                ->throw();

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fal.ai API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateVideo($prompt, $model = 'video-diffusion', $options = [])
    {
        return $this->generateImage($prompt, $model, $options);
    }

    public function queueGeneration($prompt, $model = null, $options = [])
    {
        $model = $model ?? config('fal.default_model');
        
        ProcessFalGeneration::dispatch($prompt, $model, $options)
            ->onQueue(config('fal.queue.queue'));
    }

    public function getModelStatus($modelId)
    {
        try {
            $response = Http::fal()
                ->get("/v1/models/{$modelId}/status")
                ->throw();

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fal.ai Status Check Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cancelGeneration($modelId)
    {
        try {
            $response = Http::fal()
                ->post("/v1/models/{$modelId}/cancel")
                ->throw();

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Fal.ai Cancel Error: ' . $e->getMessage());
            throw $e;
        }
    }
} 