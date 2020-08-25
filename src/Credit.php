<?php

namespace Armincms\EasyLicense;
  

class Credit extends Model 
{    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
        'expires_on' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function license()
    {
    	return $this->belongsTo(License::class);
    }  
}
