<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Generate extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'generates';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'prompt',
        'fal_model_id',
        'train_id',
        'audio_url',
        'width',
        'height',
        'status',
        'response_url',
        'status_url',
        'cancel_url',
        'queue_position',
        'requestid',
        'image_url',
        'video_url',
        'content_type',
        'inference',
        'seed',
        'credit',
        'parent',
        'user_id',
   
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function boot()
    {
        parent::boot();
        self::observe(new \App\Observers\GenerateActionObserver);
    }

    public function train()
    {
        return $this->belongsTo(Train::class, 'train_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fal()
    {
        return $this->belongsTo(Fal::class, 'fal_model_id');
    }


    public function imageTypes()
    {
        return ['video', 'upscale', 'audio', 'prompt'];
    }

    public function videoTypes()
    {
        return ['video', 'background'];
    }
    

    public static $supportedTypes = ['prompt', 'image', 'video', 'audio', 'upscale', 'train', 'background'];


    public function checkTypes($types) {

        //Get all model types from Fal
        $fals = Fal::get()->pluck('model_type')->toArray();
    
        //check if types value is in the fals array
        if(in_array($types, $fals)){
            return true;
        } else {
            return false;
        }
     }
     
     function checkFileType($filePath) {
        // Get the file extension
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
        // Generate the appropriate HTML
        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'image';
        } elseif ($fileExtension === 'mp4') {
           return 'video';
        } elseif ($fileExtension === 'mp3') {
            return 'audio';
        } else {
            return 'unsupported';
        }
    }
}
