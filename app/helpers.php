<?php

use App\MOdel\Page;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

if (! function_exists('site_config')) {
    function site_config($key) {
        try{
            $setting=new App\Models\Setting();
            $config=$setting->where('key',$key)->first();
            if($config==null)
            {
                return ucfirst($key)." configuration not found";
            }
            return $config->value;
        }
        catch(\Exception $e)
        {
             return "Exception thrown ".$e->getMessage();
        }
    }
}


if (! function_exists('layout_style_assets')) {
    function layout_style_assets($layout, array|string $files) {
        try{
            $fabricator_path='filament-fabricator/layouts/'.$layout.'/';
            $full_path="";
            if(gettype($files)=="string")
            {
                $full_path="<link rel='stylesheet' href='".asset($fabricator_path.$files)."' type='text/css'/>";
            }
            elseif(gettype($files)=="array")
            {
                foreach($files as $file)
                {
                    $full_path.="<link rel='stylesheet' href='".asset($fabricator_path.$file)."' type='text/css'/>\n";
                }
            }

            echo $full_path;
        }
        catch(\Exception $e)
        {
             return "Unable to Load Stylesheet Assets Files because: ".$e->getMessage();
        }
    }
}


if (! function_exists('layout_script_assets')) {
    function layout_script_assets($layout, array|string $files) {
        try{
            //dd($file);
            $fabricator_path='filament-fabricator/layouts/'.$layout.'/';
            $full_path="";
            if(gettype($files)=="string")
            {
                $full_path="<script' src='".asset($fabricator_path.$files)."'></script>";
            }
            elseif(gettype($files)=="array")
            {
                foreach($files as $file)
                {
                    $full_path.="<script' src='".asset($fabricator_path.$file)."'></script>\n";
                }
            }

            echo $full_path;
        }
        catch(\Exception $e)
        {
             return "Unable to Load Assets Files : because: ".$e->getMessage();
        }
    }
}

if (! function_exists('asset_url')) {
    function asset_url($layout, $file) {

            $fabricator_path='filament-fabricator/layouts/'.$layout.'/';
            return asset($fabricator_path.$file);
    }
}
