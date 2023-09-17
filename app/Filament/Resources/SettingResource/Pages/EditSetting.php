<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;


class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

}
