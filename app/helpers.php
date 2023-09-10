<?php
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
