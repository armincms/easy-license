<?php

namespace Armincms\EasyLicense;


class ConfigLocate 
{       
    public static function all($locale = null)
    {
        return Manufacturer::get()->map([static::class, 'information'])->toArray();
    }

    public static function active($menu)
    {
        return Manufacturer::actives() 
                    ->get()
                    ->map([static::class, 'information'])
                    ->toArray();
    } 

    public static function information($manufacturer)
    { 
        return [
            'id'    => $manufacturer->id,
            'title' => $manufacturer->name,
            'url'   => $manufacturer->site()->url(urldecode($manufacturer->url)),
            'active'=> boolval($manufacturer->active),
        ];
    }
    
}
