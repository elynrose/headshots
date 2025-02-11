<?php

namespace App\Observers;

use App\Models\Generate;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;
use App\Jobs\ProcessGeneratePhotos;
use App\Jobs\SendGenerateRequest;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;




class GenerateActionObserver
{
    public function created(Generate $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Generate'];

        // Fetch photos associated with the user and marked for training
        
        dispatch(new ProcessGeneratePhotos($model));  

      //  Notification::send($users, new DataChangeEmailNotification($data));
    }



    public function updated(Generate $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Generate'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
      //  Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function handle()
    {
        // Fetch photos associated with the user and marked for training
        $generate = Generate::where('id', $model->id)
            ->where('user_id', $model->user_id)
            ->where('status', 'NEW')
            ->first();
    
        if (!$generate) {
            return;
        }

        dd($generate);

       $sendRequest = $this->sendRequest($model);

       if($sendRequest){
            $this->model->update(['status' => 'PROCESSING', 'requestid' => $sendRequest->request_id]);
        }

    }

   
    /**
     * Send request to external API.
     * 
     */
    public function sendRequest($model){

        $generate = Generate::where('id', $model->id)
            ->where('user_id', $model->user_id)
            ->where('status', 'NEW')
            ->first();
    
        if (!$generate) {
            return;
        }

        $client = new \GuzzleHttp\Client();
    try {
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

        \Log::info('API Response: ' . $response->getBody());

        $responseBody = json_decode($response->getBody(), true);
        \Log::info('API Response: ' . $responseBody);
        $requestId = $responseBody['request_id'] ?? null;
        return $requestId;
    } catch (\Exception $e) {
        // Handle the exception
        \Log::error('Error sending request: ' . $e->getMessage());
        return null;
    }
    }
}