<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'content',
        'author',
        'location',
        'seo_tags',
        'meta_description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'archived',
        'active',
    ];

    protected $casts = [
        'seo_tags' => 'array',
    ];
}
