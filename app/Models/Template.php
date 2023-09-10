<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Template extends Model
{
    use HasFactory;
    protected $casts = [
        'blocks' => 'array',
        'viewable_by' => 'array'
    ];

    public function who_created()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public static function get_template()
    {
        if(Auth::user()->role_id>2)
        {
            return Template::query()->whereRaw("JSON_EXTRACT(viewable_by, '$[0].users') LIKE '%\"".Auth::user()->id."\"%'")->whereRaw("active = 1")->orWhere('created_by',Auth::user()->id);
        }
        else
        {
            return Template::query()->where('active',1);
        }
    }

}
