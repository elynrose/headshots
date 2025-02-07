<?php

namespace App\Jobs;

use App\Models\Train;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use ZipArchive;
use Exception;


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
                        if (file_exists($file->getPath())) {
                            $zip->addFile($file->getPath(), basename($file->file_name));
                        } else {
                            \Log::error("File not found: " . $file->getPath());
                        }
                    }
                }
                $zip->close();
            } else {
                throw new Exception("Unable to create ZIP file.");
            }
    
            // Process the ZIP file (e.g., upload to cloud storage)
             Storage::disk('s3')->putFileAs('train_photos', new File($path), $zipFileName, 'public');

             //Get the key
            $key = 'train_photos/' . $zipFileName;

            //get the temporary url
            $url = Storage::disk('s3')->temporaryUrl($key, now()->addMinutes(5));

             //Get the url
           // $url = Storage::disk('s3')->url($zipFileName);
            // Update the Train model with the URL
            $this->model->update(['diffusers_lora_file' => $url]);
            // Clean up the local ZIP file
            unlink($path);

        } catch (Exception $e) {
            \Log::error('ZIP processing failed: ' . $e->getMessage());
        }
    }
}    
