<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FalService;
use App\Models\Generate;
use Illuminate\Support\Facades\Log;

class ProcessFalGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prompt;
    protected $model;
    protected $options;

    public function __construct($prompt, $model, $options = [])
    {
        $this->prompt = $prompt;
        $this->model = $model;
        $this->options = $options;
    }

    public function handle(FalService $falService)
    {
        try {
            // Create a new generation record
            $generate = Generate::create([
                'prompt' => $this->prompt,
                'model' => $this->model,
                'status' => 'IN_PROGRESS',
                'options' => $this->options,
            ]);

            // Call fal.ai API
            $response = $falService->generateImage(
                $this->prompt,
                $this->model,
                $this->options
            );

            // Update generation record with response
            $generate->update([
                'status' => 'COMPLETED',
                'result' => $response,
                'image_url' => $response['image_url'] ?? null,
                'video_url' => $response['video_url'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Fal.ai Generation Error: ' . $e->getMessage());
            
            if (isset($generate)) {
                $generate->update([
                    'status' => 'ERROR',
                    'error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Fal.ai Generation Failed: ' . $exception->getMessage());
    }
} 