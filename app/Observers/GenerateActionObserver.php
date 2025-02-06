<?php

namespace App\Observers;

use App\Models\Generate;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class GenerateActionObserver
{
    public function created(Generate $model)
    {
        $data  = ['action' => 'created', 'model_name' => 'Generate'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(Generate $model)
    {
        $data  = ['action' => 'updated', 'model_name' => 'Generate'];
        $users = \App\Models\User::whereHas('roles', function ($q) {
            return $q->where('title', 'Admin');
        })->get();
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}
