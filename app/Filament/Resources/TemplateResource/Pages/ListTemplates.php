<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use App\Models\Template;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ListTemplates extends ListRecords
{
    protected static string $resource = TemplateResource::class;

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
            return Template::query()->whereRaw("JSON_EXTRACT(viewable_by, '$[0].users') LIKE '%\"".Auth::user()->id."\"%'")
           ->orWhere('created_by',Auth::user()->id)->with(['who_created']);
        }
        else
        {
            return Template::query()->with(['who_created']);
        }
    }
}
