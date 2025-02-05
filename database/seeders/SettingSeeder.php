<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key'=>'site_logo',
            'value'=>null,
            'type'=>'MultimediaPicker'
        ]);
        Setting::create([
            'key'=>'site_name',
            'value'=>"Filarcoms",
            'type'=>'TextInput'
        ]);
        Setting::create([
            'key'=>'site_description',
            'value'=>null,
            'type'=>'Textarea'
        ]);
        Setting::create([
            'key'=>'site_favicon',
            'value'=>null,
            'type'=>'CuratorPicker'
        ]);
        Setting::create([
            'key'=>'site_author',
            'value'=>null,
            'type'=>'TextInput'
        ]);
    }
}
