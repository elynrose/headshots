<?php

namespace App\Jobs;

use App\Models\Train;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use ZipArchive;
use Exception;
use GuzzleHttp\Client;

class SendTrainingRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $apiKey;
    protected $baseUrl = 'https://api.fal.ai/models/fal-ai/flux-lora-portrait-trainer';
    protected $client;

    public function __construct(Train $model)
    {
        $this->model = $model;
        $this->apiKey = env('FAL_AI_API_KEY');
        $this->baseUrl = env('FAL_AI_BASE_URL', $this->baseUrl);
        $this->client = new Client();
    }

    public function handle()
    {

        $response = $this->submitTrainingJob($this->model);
        $this->model->update(['status' => 'Processing']);
        
    }

    public function submitTrainingJob(Train $model)
    {
        $file_url = Storage::disk('s3')->get($model->temporary_amz_url);

        \Log::info('file_url: ' . $file_url);

        $response = $this->client->post($this->baseUrl, [
            'headers' => [
            'Authorization' => 'Key '. $this->apiKey,
            'Content-Type' => 'application/json',
            ],
            'json' => [
            'images_data_url' => $url,
            ],
        ]);
        $responseBody = $response->getBody()->getContents();
        \Log::info("Response: " . $responseBody);
    }

    public function getJobStatus($requestId)
    {
        $response = $this->client->get($this->baseUrl . '/queue/status', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'query' => [
                'requestId' => $requestId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getJobResult($requestId)
    {
        $response = $this->client->get($this->baseUrl . '/queue/result', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'query' => [
                'requestId' => $requestId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
