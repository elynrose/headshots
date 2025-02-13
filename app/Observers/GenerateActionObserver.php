<?php

namespace App\Observers;

use App\Models\Generate;
use App\Models\Fal;
use App\Jobs\ProcessGeneratePhotos;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Notification;

class GenerateActionObserver
{
    /**
     * Handle the Generate "created" event.
     *
     * Dispatch a job to process generate photos and optionally notify admins.
     *
     * @param  \App\Models\Generate  $model
     * @return void
     */
    public function created(Generate $model)
    {
        // Dispatch a job to process photos for the newly created model.
        dispatch(new ProcessGeneratePhotos($model));

        // Optionally, send notifications (currently commented out)
        // $data  = ['action' => 'created', 'model_name' => 'Generate'];
        // Notification::send($users, new DataChangeEmailNotification($data));
    }

    /**
     * Handle the Generate "updated" event.
     *
     * Optionally notify admins when a generate model is updated.
     *
     * @param  \App\Models\Generate  $model
     * @return void
     */
    public function updated(Generate $model)
    {
        // Optionally, send notifications on update (currently commented out)
        // $data  = ['action' => 'updated', 'model_name' => 'Generate'];
        // $users = \App\Models\User::whereHas('roles', function ($q) {
        //     return $q->where('title', 'Admin');
        // })->get();
        // Notification::send($users, new DataChangeEmailNotification($data));
    }

  
}