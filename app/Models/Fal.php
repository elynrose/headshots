<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fal extends Model
{
    use HasFactory;

    public $table = 'fal';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

   protected $fillable = [
        'title',
        'model_name',
        'model_type',
        'base_url',
        'payload',
        'icon',
        'enabled',
        'file_type',
    ];
     
    //create a static function to get an array of all the models
    public function getModelUrl($name)
    {

        switch ($name) {
            case 'flux-lora-portrait-trainer':
            return [
                'type' => 'image',
                'base_url' => 'https://queue.fal.run/fal-ai/flux-lora-portrait-trainer',
            ];
            case 'flux-lora':
            return [
                'type'=>'image',
                'base_url' => 'https://queue.fal.run/fal-ai/flux-lora',
            ];
            case 'video-01-live':
            return [
                'type'=>'video',
                'base_url' => 'https://queue.fal.run/fal-ai/video-01-live',
            ];
            default:
            return [];
        }
    }



}
