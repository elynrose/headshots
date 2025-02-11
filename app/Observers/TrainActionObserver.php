<?php

namespace App\Observers;

use App\Models\Train;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;
use App\Jobs\ProcessTrainPhotos;
use App\Jobs\SendTrainingRequest;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;




class TrainActionObserver
{

    protected $model;

    /**
     * Handle the Train "created" event.
     *
     * @param  \App\Models\Train  $model
     * @return void
     */
    public function created(Train $model)
    {
            
       dispatch(new ProcessTrainPhotos($model));  
       //$model->update(['status' => 'Processing']);

    }



}
