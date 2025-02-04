<?php

namespace App\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class ViewPage extends ViewRecord
{
    protected static string $resource = PageResource::class;

    public static function getResource(): string
    {
        return config('filament-fabricator.page-resource') ?? static::$resource;
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('visit')
                ->label(__('filament-fabricator::page-resource.actions.visit'))
                ->url(fn () => FilamentFabricator::getPageUrlFromId($this->record->id))
                ->icon('heroicon-o-external-link')
                ->openUrlInNewTab()
                ->color('success')
                ->visible(config('filament-fabricator.routing.enabled')),
                Action::make('preview')
                ->color('success')
                ->icon('heroicon-s-eye')
                ->url(function(){
                    return route('preview-page',[$this->record,'status'=>1]);
                })
                ->openUrlInNewTab()
        ];
    }
}
