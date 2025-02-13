<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class NavMenu extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $table='menus';

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class,'menu_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
