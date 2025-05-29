<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scene extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'language',
        'character_description',
        'background_description',
        'character_actions',
        'voice_over',
        'camera_angle',
        'zoom_level',
        'status',
        'campaign_id',
        'user_id'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
