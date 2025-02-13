<?php

namespace App\Filament\Resources\MenuResource\Pages;

use Biostate\FilamentMenuBuilder\Filament\Resources\MenuResource;
use App\Models\NavMenu;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make(__('filament-menu-builder::menu-builder.configure_menu'))
                ->url(fn (NavMenu $record): string => MenuResource::getUrl('build', ['record' => $record]))
                ->icon('heroicon-o-bars-3'),
            Actions\DeleteAction::make(),
        ];
    }
}
