<?php

namespace App\Jobs;

use App\Models\Generate;
use App\Models\Fal;
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
     * @var \App\Models\Generate
     */
    protected $model;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Generate  $model
     * @return void
     */
    public function __construct(Generate $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * Sends a request to the external API using the Generate model data
     * and updates the model with the API response if successful.
     *
     * @return void
     */
    public function handle()
    {
        // Send the API request and get the response
        $responseBody = $this->sendRequest($this->model);

        // If a valid response is returned, update the Generate model accordingly
        if ($responseBody) {
            // Set the 'parent' field (if applicable) and update model attributes
            $responseBody['parent'] = $this->model->id ?? null;

            $this->model->update([
                'status'       => $responseBody['status'] ?? $this->model->status,
                'requestid'    => $responseBody['request_id'] ?? null,
                'response_url' => $responseBody['response_url'] ?? null,
                'status_url'   => $responseBody['status_url'] ?? null,
                'cancel_url'   => $responseBody['cancel_url'] ?? null,
                'parent'       => $responseBody['parent'] ?? null,
            ]);
        }
    }

    /**
     * Send a request to the external API.
     *
     * Retrieves the Generate model instance along with its associated 'train' relation,
     * builds the API payload based on the Fal model type, and sends a POST request.
     *
     * @param  \App\Models\Generate  $model
     * @return array|null Returns the API response as an array on success; null on failure.
     */
    public function sendRequest($model)
    {
        // Retrieve the Generate model with its 'train' relationship, ensuring it has a "NEW" status.
        $generate = Generate::with('train')
            ->where('id', $model->id)
            ->where('user_id', $model->user_id)
            ->where('status', 'NEW')
            ->first();
            
        if (!$generate) {
            Log::warning('Generate model not found or not in NEW status.');
            return null;
        }

        Log::info('Processing Generate Model ID: ' . $generate->id);

        // Retrieve the Fal model containing external API details.
        $fal = Fal::find($generate->fal_model_id);
        if (!$fal) {
            Log::warning('Fal model not found for Generate Model ID: ' . $generate->id);
            return null;
        }

        // Prepare the payload based on the model type of the Fal record.
        if ($fal->model_type === 'image') {
            $payload = [
                'loras' => [
                    [
                        'path'  => $generate->train->diffusers_lora_file,
                        'scale' => 1,
                    ],
                ],
                'prompt'               => $generate->prompt ?? null,
                'embeddings'           => [],
                'model_name'           => $generate->title,
                'enable_safety_checker'=> true,
            ];
        } elseif ($fal->model_type === 'video') {
            $payload = [
                'prompt'    => $generate->prompt ?? null,
                'image_url' => $generate->image_url,
            ];
        } else {
            Log::warning("Unsupported Fal model type: {$fal->model_type}");
            return null;
        }

        // Create a new Guzzle HTTP client instance.
        $client = new Client();

        try {
            // Send a POST request to the external API using the base URL from the Fal model.
            $response = $client->post($fal->base_url, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            // Decode the JSON response into an array.
            $responseBody = json_decode($response->getBody(), true);
            Log::info('API Response for Generate Model ID ' . $generate->id, $responseBody);

            return $responseBody;
        } catch (\Exception $e) {
            // Log the error and return null on failure.
            Log::error('Error sending request for Generate Model ID ' . $generate->id . ': ' . $e->getMessage());
            return null;
        }
    }
}
