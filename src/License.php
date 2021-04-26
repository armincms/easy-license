<?php

namespace Armincms\EasyLicense;

use Armincms\Orderable\Contracts\Orderable;
use Armincms\Orderable\Contracts\Saleable;
use Armincms\Concerns\HasDiscount;

class License extends Model implements Orderable, Saleable
{  
    use HasDiscount, IntractsWithCredits;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'name' => '[]'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'discount' => 'json',
        'abstract' => 'json',
        'name' => 'json',
    ];

    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'easy-license.license', 'easy-license.license.list', '*'
            ]
        ],
    ];  
    
    /**
     * Query the related Credit.
     * 
     * @return \Illuminate\Database\Eloqeunt\Relations\HasMany
     */
    public function credits()
    {
    	return $this->hasMany(Credit::class);
    } 

    /**
     * Query the related Product.
     * 
     * @return \Illuminate\Database\Eloqeunt\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }  

    /**
     * Query the related Duration.
     * 
     * @return \Illuminate\Database\Eloqeunt\Relations\BelongsTo
     */
    public function duration()
    {
        return $this->belongsTo(\Armincms\Duration\Duration::class);
    }   

    /**
     * Get the sale price currency.
     * 
     * @return decimal
     */
    public function currency(): string
    {
        return Nova\EasyLicense::currency();
    }

    /**
     * Get the sale price of the item.
     * 
     * @return decimal
     */
    public function salePrice(): float
    {
        return $this->applyDiscount(floatval($this->price));
    }

    /**
     * Get the real price of the item.
     * 
     * @return decimal
     */
    public function oldPrice(): float
    {
        return $this->price;
    }

    /**
     * Get the item name.
     * 
     * @return decimal
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the item description.
     * 
     * @return decimal
     */
    public function description(): string
    {
        return $this->name();
    }
}
