<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Layout;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Creagia\FilamentCodeField\CodeField;
use Illuminate\Support\Str;

class viewLayout extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = LayoutResource::class;

    protected static string $view = 'filament.resources.layout-resource.pages.view-layout';

    public Layout $record;

    public $layout_view;
    public $view_file;

    public function mount()
    {
        $view_path="";

        //dd($view_path);

        if(!file_exists(resource_path('views/components/filament-fabricator/layouts/'.$this->record->name.'.blade.php')))
        {
            if(file_exists(resource_path('views/components/filament-fabricator/layouts/'.Str::kebab($this->record->name).'.blade.php')))
            {
                $this->view_file=resource_path('views/components/filament-fabricator/layouts/'.Str::kebab($this->record->name).'.blade.php');
                $view_path=file_get_contents(resource_path('views/components/filament-fabricator/layouts/'.Str::kebab($this->record->name).'.blade.php'));
            }
            else
            {
                $this->view_file=resource_path('views/components/filament-fabricator/layouts/'.ucfirst($this->record->name).'.blade.php');
                $view_path=file_get_contents(resource_path('views/components/filament-fabricator/layouts/'.ucfirst($this->record->name).'.blade.php'));
            }

        }

        else{
            $this->view_file=resource_path('views/components/filament-fabricator/layouts/'.$this->record->name.'.blade.php');
            $view_path=file_get_contents(resource_path('views/components/filament-fabricator/layouts/'.$this->record->name.'.blade.php'));
        }

        //dd(view('components.filament-fabricator.page-blocks.'.$this->record->name)->render());
        $this->form->fill([
            'layout_view' => $view_path,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [

            Card::make()->schema([
                CodeField::make('layout_view')
                ->setLanguage(CodeField::HTML, CodeField::JS, CodeField::PHP)
                ->withLineNumbers()
                ->hint('HTML and JavaScript Editor'),
                CodeField::make('block_resource')
                ->setLanguage(CodeField::PHP)
                ->withLineNumbers()
                ->hint('PHP Editor'),
                /*Select::make('layouts')
                ->label(__('filament-fabricator::page-resource.labels.layout'))
                ->options(FilamentFabricator::getLayouts())
                ->default('default')
                ->required(),*/
            ])
        ];
    }

    public function save_data()
    {
        try{
             if(!file_exists($this->view_file))
            {
                fopen($this->view_file,'w');
            }

            File::put($this->view_file, $this->layout_view);
            Notification::make()
            ->title('Saved Successfully')
            ->success()
            ->send();
        }
        catch(\Exception $e)
        {
            Notification::make()
            ->title('unable to save file because: '.$e->getMessage())
            ->danger()
            ->send();
        }

    }
}
