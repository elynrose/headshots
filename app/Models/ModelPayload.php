<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelPayload extends Model
{
    use HasFactory;

    public $table = 'model_payloads';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'model_type',
        'payload_template',
        'created_at',
        'updated_at',
    ];
}

