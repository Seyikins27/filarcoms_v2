<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateLayout extends CreateRecord
{
    protected static string $resource = LayoutResource::class;

    protected function afterCreate(): void
    {
       // dd($this->data);
        try{
            $asset_path = public_path('filament-fabricator/layouts/'.$this->data['name']);
            $layout_file_exists=file_exists(resource_path('views/components/filament-fabricator/layouts/'.$this->data['name'].'.blade.php'));
            if(!File::isDirectory($asset_path) && $layout_file_exists ==false){
                Artisan::call('filament-fabricator:layout',['name'=>$this->data['name']]);
                Notification::make()
                ->title('Layout Created')
                ->success()
                ->send();
                File::makeDirectory($asset_path, 0777, true, true);
            }
            else
            {
                throw new \Exception($this->data['name'].'Layout Directory already exists');
            }

        }
        catch (\Exception $e) {
            Notification::make()
            ->title('unable to create layout because: '.$e->getMessage())
            ->danger()
            ->send();
            $this->halt();
          }
    }
}
