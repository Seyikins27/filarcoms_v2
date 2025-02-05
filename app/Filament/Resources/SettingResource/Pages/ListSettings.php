<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ListSettings extends ListRecords implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SettingResource::class;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getActions(): array
    {
        $settings=Setting::all()->pluck('value','key')->toArray();
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Set Configurations')
            ->mountUsing(function (Forms\ComponentContainer $form) use ($settings){
                $form->fill($settings);
            })
            ->action(function(array $data) {
                foreach($data as $key=>$value)
                {
                    Setting::where('key',$key)->update(['value'=>$value]);
                }
             })->form(ListSettings::getFormSchema())
        ];
    }

    public function getFormSchema(): array
    {
        $all_settings=Setting::all();
        $settings_form=[];
        $test_array=[];
        $namespace='Filament\\Forms\\Components';
        foreach($all_settings as $setting)
        {
            if($setting->type=="CuratorPicker")
            {
                $namespace='Awcodes\\Curator\\Components\\Forms';
            }
            elseif($setting->type=="MultimediaPicker")
            {
                $namespace='App\\Forms\\Components';
            }
            else{
                $namespace='Filament\\Forms\\Components';
            }

            $form="$namespace\\$setting->type";
            //$instantiate_form=new $form;
            $setting_type=$form::make($setting->key)->label(ucfirst($setting->key));
           // echo $namespace;
            array_push($settings_form,$setting_type);
        }
        //dump($test_array);
        return $settings_form;
    }
}
