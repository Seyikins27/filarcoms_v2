<?php

namespace App\Filament\Resources\BlocksResource\Pages;

use App\Filament\Resources\BlocksResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;

class CreateBlocks extends CreateRecord
{
    protected static string $resource = BlocksResource::class;

    protected function beforeCreate(): void
    {
        //dd($this->data);
        try{
            Artisan::call('filament-fabricator:block',['name'=>$this->data['name']]);
            Notification::make()
            ->title('Block Created')
            ->success()
            ->send();
        }
        catch (\Exception $e) {
            Notification::make()
            ->title('unable to create block because: '.$e->getMessage())
            ->danger()
            ->send();
            $this->halt();
          }
    }
}
