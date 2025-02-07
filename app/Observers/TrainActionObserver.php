<?php

namespace App\Observers;

use App\Models\Train;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;
use App\Jobs\ProcessTrainPhotos;


class TrainActionObserver
{
    public function created(Train $model)
    {
            
        dispatch(new ProcessTrainPhotos($model));       

    }

    public function updated(Train $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Train'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        
     //   Notification::send($users, new DataChangeEmailNotification($data));
    }
}
