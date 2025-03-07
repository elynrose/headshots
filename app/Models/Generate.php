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
        'created_at',
        'updated_at',
        'deleted_at',
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
}
