<?php

namespace App\Jobs;

use App\Models\Train;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\File;
use ZipArchive;
use Exception;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class ProcessTrainPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The Train model instance.
     *
     * @var Train
     */
    protected $model;

    /**
     * Create a new job instance.
     *
     * @param Train $model
     * @return void
     */
    public function __construct(Train $model)
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
        // Fetch photos associated with the user and marked for training
        $photos = \App\Models\Photo::with(['user', 'media'])
            ->where('user_id', $this->model->user_id)
            ->where('use_for_training', 1)
            ->get();
    
        if ($photos->isEmpty()) {
            return;
        }
    
        $zip = new ZipArchive;
        $zipFileName = 'train_photos_' . uniqid() . '.zip';
        $tempDirectory = storage_path('app/temp/');
    
        // Ensure the temp directory exists
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0775, true);
        }
    
        $path = $tempDirectory . $zipFileName;

        // Create a new ZIP file
        try {
            if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
                // Add each photo file to the ZIP archive
                foreach ($photos as $media) {
                    $files = $media->getMedia('photo');
                    foreach ($files as $file) {
                        $s3Url = $file->getUrl();
                        $fileContents = file_get_contents($s3Url);
                        if ($fileContents !== false) {
                            $zip->addFromString(basename($s3Url), $fileContents);
                        } else {
                            \Log::error("Failed to download file from S3: " . $s3Url);
                        }
                    }
                }
                $zip->close();
            } else {
                throw new Exception("Unable to create ZIP file.");
            }
    
            // Upload ZIP file to S3 storage
            Storage::disk('s3')->putFileAs('train_photos', new File($path), $zipFileName, ['visibility' => 'public']);
             
            // Generate file URL
            $key = $zipFileName;
            $url = Storage::disk('s3')->url('train_photos/' . $key);
            \Log::info("ZIP file uploaded successfully. URL: " . $url);
            
            // Update model with the ZIP file URL and key
            $this->model->update(['zipped_file_url' => $url, 'temporary_amz_url' => $key, 'file_size' => Storage::disk('s3')->size('train_photos/' . $key), 'status' => 'NEW']);
            
            // Remove the local ZIP file after upload
            unlink($path);

            // Submit the training job to an external API
            $this->submitTrainingJob($this->model);

        } catch (Exception $e) {
            \Log::error('ZIP processing failed: ' . $e->getMessage());
            return false;
        }
    }



     /**
     * Submit the training job to an external API.
     *
     * @param string $url
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function submitTrainingJob($train)
    {
        try {
            // Make a POST request to submit the training job
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://queue.fal.run/fal-ai/flux-lora-fast-training', [
                'headers' => [
                    'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'images_data_url' => $train->zipped_file_url,
                ],
            ]);
            
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            if ($responseData !== null) {
                // Update model with response data from training API
                $train->status = $responseData['status'];
                $train->requestid = $responseData['request_id'];
                $train->status_url = $responseData['status_url'] ?? null;
                $train->response_url = $responseData['response_url'] ?? null;
                $train->cancel_url = $responseData['cancel_url'] ?? null;
                $train->queue_position = $responseData['queue_position'] ?? null;
                $train->save();
            } else {
                \Log::error('Failed to decode JSON response: ' . $responseData);
            }

        } catch (Exception $e) {
            //$train->status = "ERROR";
            $train->update(['status' => 'ERROR']);
            \Log::error('Training job submission failed: ' . $e->getMessage());
        }
    }
   
}
