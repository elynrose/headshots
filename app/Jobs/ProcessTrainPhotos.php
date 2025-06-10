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
use App\Models\Credit;




class ProcessTrainPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * The queue connection that should handle the job.
     *
     * @var string
     */
    public $connection = 'redis';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string
     */
    public $queue = 'training';

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
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        $this->model->update([
            'status' => 'ERROR',
            'error_log' => $exception->getMessage()
        ]);

        \Log::error('Training job failed: ' . $exception->getMessage());
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [60, 180, 300]; // Retry after 1, 3, and 5 minutes
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
        // Fetch photos associated with the user and marked for training
        $photos = \App\Models\Photo::with(['user', 'media'])
            ->where('user_id', $this->model->user_id)
            ->where('use_for_training', 1)
            ->get();
    
        if ($photos->isEmpty()) {
                $this->model->update(['status' => 'ERROR', 'error_log' => 'No photos found for training']);
                return;
            }

            // Validate and prepare images
            $validImages = $this->validateAndPrepareImages($photos);
            if (empty($validImages)) {
                $this->model->update(['status' => 'ERROR', 'error_log' => 'No valid images found for training']);
            return;
        }
    
            // Create ZIP file with optimized images
        $zipFileName = 'train_photos_' . uniqid() . '.zip';
        $tempDirectory = storage_path('app/temp/');
    
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0775, true);
        }
    
        $path = $tempDirectory . $zipFileName;

            // Create ZIP with optimized images
            if ($this->createOptimizedZip($validImages, $path)) {
                // Upload to cloud storage
                Storage::disk('cloud')->putFileAs('train_photos', new File($path), $zipFileName, ['visibility' => 'public']);
                
                // Generate file URL
                $url = Storage::disk('cloud')->url('train_photos/' . $zipFileName);
                
                // Update model with ZIP file URL
                $this->model->update([
                    'zipped_file_url' => $url,
                    'temporary_amz_url' => $zipFileName,
                    'file_size' => Storage::disk('cloud')->size('train_photos/' . $zipFileName),
                    'status' => 'NEW'
                ]);
                
                // Clean up temporary file
                unlink($path);

                // Submit training job
                $this->submitTrainingJob($this->model);
                        } else {
                $this->model->update(['status' => 'ERROR', 'error_log' => 'Failed to create ZIP file']);
            }

        } catch (Exception $e) {
            \Log::error('Training process failed: ' . $e->getMessage());
            $this->model->update(['status' => 'ERROR', 'error_log' => $e->getMessage()]);
        }
    }

    /**
     * Validate and prepare images for training
     */
    private function validateAndPrepareImages($photos)
    {
        $validImages = [];
        
        foreach ($photos as $photo) {
            $media = $photo->getFirstMedia('photo');
            if (!$media) continue;

            try {
                $imageUrl = $media->getUrl();
                $imageData = file_get_contents($imageUrl);
                
                if ($imageData === false) {
                    \Log::warning("Failed to download image: {$imageUrl}");
                    continue;
                }

                // Validate image format and size
                $imageInfo = getimagesizefromstring($imageData);
                if ($imageInfo === false) {
                    \Log::warning("Invalid image format: {$imageUrl}");
                    continue;
                }

                // Check minimum dimensions
                if ($imageInfo[0] < 512 || $imageInfo[1] < 512) {
                    \Log::warning("Image too small: {$imageUrl}");
                    continue;
                }

                // Optimize image if needed
                $optimizedData = $this->optimizeImage($imageData, $imageInfo);
                
                $validImages[] = [
                    'data' => $optimizedData,
                    'name' => basename($imageUrl)
                ];
            } catch (Exception $e) {
                \Log::error("Error processing image {$photo->id}: " . $e->getMessage());
                continue;
            }
        }

        return $validImages;
    }

    /**
     * Optimize image data
     */
    private function optimizeImage($imageData, $imageInfo)
    {
        // Create image resource
        $image = imagecreatefromstring($imageData);
        
        // Resize if too large (max 1024px on longest side)
        $maxDimension = 1024;
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = floor($height * ($maxDimension / $width));
            } else {
                $newHeight = $maxDimension;
                $newWidth = floor($width * ($maxDimension / $height));
            }
    
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $resized;
        }

        // Output optimized image
        ob_start();
        imagejpeg($image, null, 85); // 85% quality
        $optimizedData = ob_get_clean();
        
        imagedestroy($image);
        if (isset($resized)) {
            imagedestroy($resized);
        }

        return $optimizedData;
    }

    /**
     * Create ZIP file with optimized images
     */
    private function createOptimizedZip($images, $path)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($path, ZipArchive::CREATE) !== TRUE) {
            return false;
        }

        foreach ($images as $image) {
            $zip->addFromString($image['name'], $image['data']);
    }

        return $zip->close();
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
            if (is_null($responseData)) {
                $train->update(['status' => 'IN_QUEUE']);
                $train->save();
            } else {
            // Update model with response data from training API
            $train->status = $responseData['status'];
            $train->requestid = $responseData['request_id'];
            $train->status_url = $responseData['status_url'] ?? null;
            $train->response_url = $responseData['response_url'] ?? null;
            $train->cancel_url = $responseData['cancel_url'] ?? null;
            $train->queue_position = $responseData['queue_position'] ?? null;
            $train->save();  
            
            //Deduce Credits
            $credits = Credit::where('email', Auth::user()->email)->first();
            if ($credits) {
                $credits->credit = $credits->points - env('FIXED_COST'); // Deduct 1 credit
                $credits->save();
            }
        } 
        } catch (Exception $e) {
            //$train->status = "ERROR";
            $train->update(['status' => 'IN_QUEUE']);
            \Log::error('Training job submission failed: ' . $e->getMessage());
        }
    }
   
}
