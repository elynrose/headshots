<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Train extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'trains';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'requestid',
        'title',
        'status',
        'diffusers_lora_file',
        'config_file',
        'zipped_file_url',
        'temporary_amz_url',
        'response_url',
        'cancel_url',
        'status_url',
        'queue_position',
        'file_size',
        'error_log',
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
        self::observe(new \App\Observers\TrainActionObserver);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
