<?php

namespace App\Jobs;

use App\Models\Train;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Exception;

class SendTrainingRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The model instance associated with the job.
     *
     * @var Train
     */
    protected $model;

    /**
     * API Key for authentication.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Base URL for the API endpoint.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * HTTP client instance.
     *
     * @var Client
     */
    protected $client;

    /**
     * Create a new job instance.
     *
     * @param Train $model The training model instance.
     * @return void
     */
    public function __construct(Train $model)
    {
        $this->model = $model;
        $this->apiKey = env('FAL_AI_API_KEY');
        
        // Set the API base URL from environment or use default
        $this->baseUrl = env('FAL_AI_BASE_URL', 'https://api.fal.ai/models/fal-ai/flux-lora-portrait-trainer');
        
        // Initialize HTTP client
        $this->client = new Client();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://queue.fal.run/fal-ai/flux-lora-fast-training', [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'images_data_url' => $this->model->zipped_file_url,
                ],
            ]);
          
            //    $response = $this->submitTrainingJob($url);
                $responseBody = $response->getBody()->getContents();
                $responseData = json_decode($responseBody, true);
                \Log::info("API Response: " . $responseData);
                if ($responseData !== null) {
                    // Update model with response data from training API
                    $train->status = $responseData['status'];
                    $train->requestid = $responseData['request_id'];
                    $train->status_url = $responseData['status_url'];
                    $train->response_url = $responseData['response_url'];
                    $train->cancel_url = $responseData['cancel_url'];
                    $train->queue_position = $responseData['queue_position'];
                    $train->save();
                } else {
                    \Log::error('Failed to decode JSON response: ' . $responseData);
                }

            return $response;
        } catch (Exception $e) {
            \Log::error('Training job submission failed: ' . $e->getMessage());
        }
    }


}
