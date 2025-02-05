<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocks extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function image()
    {
        return $this->belongsTo(Media::class,'block_image','id');
    }
}
