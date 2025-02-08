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




class ProcessTrainPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public function __construct(Train $model)
    {
        $this->model = $model;

    }

    public function handle()
    {
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
        \Log::info("Creating ZIP file at: " . $path);
    
        try {
            if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
        
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
    
            // Process the ZIP file (e.g., upload to cloud storage)
             Storage::disk('s3')->putFileAs('train_photos', new File($path), $zipFileName, ['visibility' => 'public']);
             
             //Get the key
            $key = $zipFileName;
            // Get the URL of the uploaded file
            $url = Storage::disk('s3')->url('train_photos/' . $key);
            \Log::info("ZIP file uploaded successfully. URL: " . $url);
            // Update the model with the key so you can pull the url
            $this->model->update(['zipped_file_url' => $url, 'temporary_amz_url' => $key]);
            // Clean up the local ZIP file
            unlink($path);

            \Log::info("ZIP file created and uploaded successfully. URL: " . $url);
           
            try  {
                //Get the url of the uploaded file
                $response = $this->submitTrainingJob($url);
                $responseBody = $response->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

            if ($responseData !== null ) {
                // Update the model with the response data    
                $this->model->status = $responseData['status'];
                $this->model->requestid = $responseData['request_id'];
                $this->model->status_url = $responseData['status_url'];
                $this->model->cancel_url = $responseData['cancel_url'];
                $this->model->queue_position = $responseData['queue_position'];
                $this->model->save();
            } else {
                \Log::error('Failed to decode JSON response: ' . $responseData);
            }
            } catch (Exception $e) {
                \Log::error('Training job submission failed: ' . $e->getMessage());
            }


  

        } catch (Exception $e) {
            return false;
            
            \Log::error('ZIP processing failed: ' . $e->getMessage());
        }
    }


    public function submitTrainingJob($url)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://queue.fal.run/fal-ai/flux-lora-fast-training', [
            'headers' => [
                'Authorization' => 'Key ' . env('FAL_AI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'images_data_url' => $url,
            ],
            ]);
            
            return $response;;

        } catch (Exception $e) {
            \Log::error('Training job submission failed: ' . $e->getMessage());
        }
    }
}    
