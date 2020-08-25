<?php

namespace Armincms\EasyLicense;
  

class Manual extends Model 
{    
	use HasSelling {
        asSold as markAsSold;
    }
	
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json', 
        'sold_at' => 'datetime', 
    ];

    public function card()
    {
    	return $this->belongsTo(Card::class);
    } 

    /**
     * Mark the model with the "sale" value.
     *
     * @return $this
     */
    public function asSold(bool $sale = true)
    {
        $this->forceFill([
            'sold_at' => now()
        ])->save();

        return $this->markAsSold();
    }
}
