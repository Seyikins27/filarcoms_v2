<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['templates']);
        $data['published']=0;
        $data['created_by']=Auth::user()->id;
        if($data['published']!=true || $data['published']!=1)
        {
            $data['preview_blocks'] = $data['blocks'];
        }
        return $data;
    }
}
