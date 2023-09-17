<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Models\Page;
use App\Models\Template;
use Filament\Notifications\Notification;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function beforeFill():void
    {
        if(Auth::user()->role_id>2)
        {
            $record=$this->record->viewable_by!=null?(object)$this->record->viewable_by[0]:null;
            //dd($record);
            if($record===null)
            {
                abort(403);
            }
            abort_unless(in_array(Auth::user()->id,$record->users) || in_array(Auth::user()->organogram_id,$record->organogram),403);
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
          $data['preview_blocks']=$data['blocks'];
          $data['blocks']=$this->record->blocks;

        return $data;
    }

    protected function getActions(): array
    {

        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Action::make('visit')
                ->label(__('filament-fabricator::page-resource.actions.visit'))
                ->url(fn () => FilamentFabricator::getPageUrlFromId($this->record->id))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->openUrlInNewTab()
                ->color('success')
                ->visible(config('filament-fabricator.routing.enabled')),
            Action::make('save')
                ->action('save')
                ->label('Save'),
            Actions\Action::make('save_template')
                ->color('success')
                ->label('Save as Template')
                ->action('save_template'),
            Action::make('preview')
                ->color('success')
                ->icon('heroicon-s-eye')
                ->url(function(){
                    return route('preview-page',[$this->record,'status'=>1]);
                })
                ->openUrlInNewTab(),
            Action::make('publish')
                ->requiresConfirmation()
                ->color('warning')
                ->action('publish')
                ->hidden( fn($record) =>! Auth::user()->can_publish())
        ];
    }

    function publish()
    {
        if(Auth::user()->can_publish())
        {
        $this->record->published=true;
        $this->record->blocks=$this->record->preview_blocks;
        //$this->data['blocks']=$this->record->preview_blocks;
        $this->record->update();
        Notification::make()
            ->title('Page Published Successfully')
            ->success()
            ->send();
            return $this->getResource()::getUrl('edit',['record',$this->record->id]);
        }
        else
        {
            Notification::make()
            ->title('You are not authorised to publish pages')
            ->warning()
            ->send();
            return $this->getResource()::getUrl('edit',$this->record->id);
        }
    }

    function save_template()
    {
        $template_exists=Template::where('name',$this->record->title)->first();
        if($template_exists)
        {
            $data=[
                'name'=>$this->record->title,
                'layout'=>$this->record->layout,
                'blocks'=>$this->record->blocks,
            ];
            $template_exists->update($data);
            Notification::make()
            ->title('Template Updated Succesfully')
            ->success()
            ->send();
            return $this->getResource()::getUrl('edit',['record'=>$this->record->id]);
        }
        else
        {
            $data=[
                'name'=>$this->record->title,
                'layout'=>$this->record->layout,
                'blocks'=>$this->record->blocks,
                'viewable_by'=>$this->record->viewable_by,
                'active'=>0,
                'created_by'=>Auth::user()->id
            ];
              $save=Template::create($data);
              Notification::make()
              ->title('Template Created Successfully')
              ->success()
              ->send();
              return $this->getResource()::getUrl('edit',['record'=>$this->record->id]);
        }

    }
}
