<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;


class ModelPayload extends Model
{
    use HasFactory;

    public $table = 'model_payloads';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const FILE_TYPE_SELECT = [
        'audio' => 'Audio',
        'image' => 'Image',
        'video' => 'Video',
    ];

    protected $fillable = [
        'model_type',
        'payload_template',
        'file_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

