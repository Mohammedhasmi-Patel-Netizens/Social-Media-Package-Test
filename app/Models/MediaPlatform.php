<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('uid', 'name', 'slug', 'description', 'configuration', 'status', 'is_feature', 'is_integrated')]
class MediaPlatform extends Model
{
    use HasFactory;

    protected $casts = [
        'configuration' => 'object',
    ];
}
