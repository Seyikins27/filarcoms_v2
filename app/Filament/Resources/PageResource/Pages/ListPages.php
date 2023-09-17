<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Z3d0X\FilamentFabricator\Models\Page;


class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if(Auth::user()->role_id>2)
        {
            return Page::query()->whereRaw("JSON_EXTRACT(viewable_by, '$[0].users') LIKE '%\"".Auth::user()->id."\"%'")->orWhere('created_by',Auth::user()->id);
        }
        else
        {
            return Page::query();
        }
    }
}
