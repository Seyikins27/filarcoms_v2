<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable=['title','slug','image','seo_tags','meta_description','content','author','tags','archived','active'];
    protected $casts=[
        'seo_tags'=>'array',
        'tags'=>'array'
    ];
}
