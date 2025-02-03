<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as Contract;
use Z3d0X\FilamentFabricator\Models\Concerns\HandlesPageUrls;

class Page extends Model implements Contract
{
    use HasFactory, HandlesPageUrls;

    public function __construct(array $attributes = [])
    {
        if (blank($this->table)) {
            $this->setTable(config('filament-fabricator.table_name', 'pages'));
        }

        parent::__construct($attributes);
    }

    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'layout',
        'seo_tags',
        'meta_description',
        'parent_id',
        'published',
        'viewable_by',
        'preview_blocks',
        'created_by'
    ];

    protected $casts = [
        'blocks' => 'array',
        'parent_id' => 'integer',
        'viewable_by'=> 'array',
        'seo_tags' => 'array',
        'preview_blocks' => 'array'
    ];



    protected static function booted()
    {
        static::saved(fn () => Cache::forget('filament-fabricator::page-urls'));
        static::deleted(fn () => Cache::forget('filament-fabricator::page-urls'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->select('id', 'slug', 'title', 'parent_id')->with('children:id,slug,title,parent_id');
    }

    public function who_created(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
