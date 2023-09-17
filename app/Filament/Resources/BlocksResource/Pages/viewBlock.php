<?php

namespace App\Filament\Resources\BlocksResource\Pages;

use App\Filament\Resources\BlocksResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Blocks;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class viewBlock extends Page implements HasForms
{
    protected static string $resource = BlocksResource::class;

    protected static string $view = 'filament.resources.blocks-resource.pages.view-block';

    use InteractsWithForms;

    public Blocks $record;

    public $block_view;
    public $block_resource;
    public $view_file;
    public $resource_file;
    //public $layout;


    public function mount()
    {
        $resource_path="";

        $view_path="";

        //dd(resource_path('views/components/filament-fabricator/page-blocks/'.Str::kebab($this->record->name).'.blade.php'));
        //dd(Str::kebab($this->record->name));
        //dd(file_exists(app_path('Filament/Fabricator/PageBlocks/'.$this->record->name.'.php')));

        if(file_exists(app_path('Filament/Fabricator/PageBlocks/'.$this->record->name.'.php'))!=true)
        {
            $this->resource_file=app_path('Filament/Fabricator/PageBlocks/'.ucfirst($this->record->name).'.php');
            $resource_path=file_get_contents(app_path('Filament/Fabricator/PageBlocks/'.ucfirst($this->record->name).'.php'));
        }
        else{
            $this->resource_file=app_path('Filament/Fabricator/PageBlocks/'.$this->record->name.'.php');
            $resource_path=file_get_contents(app_path('Filament/Fabricator/PageBlocks/'.$this->record->name.'.php'));
        }


        if(file_exists(resource_path('views/components/filament-fabricator/page-blocks/'.$this->record->name.'.blade.php'))!=true)
        {
            if(file_exists(resource_path('views/components/filament-fabricator/page-blocks/'.Str::kebab($this->record->name).'.blade.php'))==true)
            {
                $this->view_file=resource_path('views/components/filament-fabricator/page-blocks/'.Str::kebab($this->record->name).'.blade.php');
                $view_path=file_get_contents(resource_path('views/components/filament-fabricator/page-blocks/'.Str::kebab($this->record->name).'.blade.php'));
            }
            else
            {
                $this->view_file=resource_path('views/components/filament-fabricator/page-blocks/'.ucfirst($this->record->name).'.blade.php');
                $view_path=file_get_contents(resource_path('views/components/filament-fabricator/page-blocks/'.ucfirst($this->record->name).'.blade.php'));
            }

        }

        else{
            $this->view_file=resource_path('views/components/filament-fabricator/page-blocks/'.$this->record->name.'.blade.php');
            $view_path=file_get_contents(resource_path('views/components/filament-fabricator/page-blocks/'.$this->record->name.'.blade.php'));
        }

        //dd(view('components.filament-fabricator.page-blocks.'.$this->record->name)->render());
        $this->form->fill([
            'block_view' => $view_path,
            'block_resource' => $resource_path,
        ]);
    }

    protected function getFormSchema(): array
    {
        //dd(CodeField::make('smething'));
        return [
            Section::make()->schema([
                CodeField::make('block_view')
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
            if(!file_exists($this->resource_file))
            {
                fopen($this->resource_file,'w');

            }

             if(!file_exists($this->view_file))
            {
                fopen($this->view_file,'w');

            }
            File::put($this->resource_file, $this->block_resource);
            File::put($this->view_file, $this->block_view);

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
