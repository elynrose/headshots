<?php

namespace App\Observers;

use App\Models\Train;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class TrainActionObserver
{
    public function created(Train $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Train'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(Train $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Train'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}
