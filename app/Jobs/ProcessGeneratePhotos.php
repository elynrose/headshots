<?php

namespace App\Jobs;

use App\Models\Generate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ProcessGeneratePhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Generate model instance.
     *
     * @var Generate
     */
    protected $model;

    /**
     * Create a new job instance.
     *
     * @param Generate $model
     * @return void
     */
    public function __construct(Generate $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send request to external API and get the response
        $responseBody = $this->sendRequest($this->model);
        \Log::info('API Response: ' . json_encode($responseBody));
        // If response is received, update the model with the response data
        if ($responseBody) {
            $this->model->update([
                'status' => $responseBody['status'],
                'requestid' => $responseBody['request_id'],
                'response_url' => $responseBody['response_url'],
                'status_url' => $responseBody['status_url'],
                'cancel_url' => $responseBody['cancel_url']
            ]);
        }
    }

    /**
     * Send request to external API.
     *
     * @param Generate $model
     * @return array|null
     */
    public function sendRequest($model)
    {
        // Retrieve the Generate model instance with specific conditions
        $generate = Generate::where('id', $model->id)
            ->where('user_id', $model->user_id)
            ->where('status', 'NEW')
            ->first();

        // If no matching Generate model is found, return null
        if (!$generate) {
            return null;
        }

        // Create a new Guzzle HTTP client
        $client = new Client();

        try {
            // Send a POST request to the external API
            $response = $client->post('https://queue.fal.run/fal-ai/flux-lora', [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'loras' => [
                        [
                            'path' => $generate->train->diffusers_lora_file,
                            'scale' => 1
                        ]
                    ],
                    'prompt' => $generate->prompt ?? null,
                    'embeddings' => [],
                    'model_name' => $generate->title,
                    'enable_safety_checker' => true
                ],
            ]);

            // Log the API response
            Log::info('API Response: ' . $response->getBody());

            // Decode the JSON response body
            $responseBody = json_decode($response->getBody(), true);

            // Return the response body
            return $responseBody;
            
        } catch (\Exception $e) {
            // Handle the exception and log the error message
            Log::error('Error sending request: ' . $e->getMessage());
            return null;
        }
    }
}
