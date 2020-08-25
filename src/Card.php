<?php

namespace Armincms\EasyLicense;
  

class Card extends Model 
{   
    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'easy-license.card', 'easy-license.card.list', '*'
            ]
        ],
    ]; 

    public function license()
    {
    	return $this->belongsTo(License::class);
    } 

    public function manuals()
    {
        return $this->hasMany(Manual::class);
    }
}
