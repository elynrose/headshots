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
use App\Models\PayloadGenerator;

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
        try {
            // Send the API request and get the response
            $responseBody = $this->sendRequest($this->model);

            // If a valid response is returned, update the Generate model accordingly
            if ($responseBody) {
            // Update the Generate model with the response data
            $this->model->update([
                'status'         => $responseBody['status'] ?? $this->model->status,
                'requestid'      => $responseBody['request_id'] ?? null,
                'response_url'   => $responseBody['response_url'] ?? null,
                'status_url'     => $responseBody['status_url'] ?? null,
                'cancel_url'     => $responseBody['cancel_url'] ?? null,
                'queue_position' => $responseBody['queue_position'] ?? null,
            ]);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error processing Generate Model ID ' . $this->model->id . ': ' . $e->getMessage());
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
        // Retrieve the Generate model with its 'train' relation if content_type is 'train'
        if ($model->content_type === 'train') {
            $generate = Generate::with('train')
                ->where('id', $model->id)
                ->where('user_id', $model->user_id)
                ->where('status', 'NEW')
                ->first();
        } else {
            $generate = Generate::where('id', $model->id)
                ->where('user_id', $model->user_id)
                ->where('status', 'NEW')
                ->first();
        }

        // Log a warning if the Generate model is not found or not in NEW status
        if (!$generate) {
            return null;
        }

        // Retrieve the Fal model containing external API details
        $fal = Fal::where('id', $generate->fal_model_id)->first();

        // Prepare the payload based on the model type of the Fal record
        $payloadGenerator = new PayloadGenerator();
        $supportedTypes = ['prompt', 'image', 'video', 'audio', 'upscale', 'train', 'background'];

        if (in_array($fal->model_type, $supportedTypes)) {
            $payload = $payloadGenerator->generatePayload($fal, $generate);
        } else {
            return null;
        }

        // Create a new Guzzle HTTP client instance
        $client = new Client();
        $urlWithWebhook = $fal->base_url . '?webhook=' . env('APP_URL') . '/webhook';
        \Log::info('Key ' . env('FAL_AI_API_KEY'));
        try {
            // Send a POST request to the external API using the base URL from the Fal model
            $response = $client->post($urlWithWebhook, [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            // Decode the JSON response into an array
            $responseBody = json_decode($response->getBody(), true);

            return $responseBody;
        } catch (\Exception $e) {
            // Log the error and return null on failure
            Log::error('Error sending request for Generate Model ID ' . $generate->id . ': ' . $e->getMessage());
            return null;
        }
    }
}
