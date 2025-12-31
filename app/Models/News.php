<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'content',
        'author',
        'seo_tags',
        'meta_description',
        'archived',
        'active',
    ];

    protected $casts = [
        'seo_tags' => 'array',
    ];
}
