<?php

namespace Armincms\EasyLicense;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes, Builder};  
use Zareismail\Markable\{Markable, HasActivation}; 
use Spatie\MediaLibrary\HasMedia\HasMedia; 
use Armincms\Concerns\{IntractsWithMedia, Authorization};
use Armincms\Targomaan\Concerns\InteractsWithTargomaan;
use Armincms\Contracts\Authorizable;
use Armincms\Fields\TargomaanField;

abstract class Model extends LaravelModel implements HasMedia, Authorizable
{   
    use SoftDeletes, IntractsWithMedia, InteractsWithTargomaan, HasActivation, Authorization, Markable;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seo' => 'json',
        'name' => 'json',
        'abstract' => 'json',
    ]; 
    
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = parent::getTable();

        return Str::startsWith($table, 'el_') ? $table : "el_{$table}";
    } 

    public function featuredImages()
    {
        return $this->getConversions(
            $this->getFirstMedia('image'), config('easy-license.product.schemas', ['main', 'thumbnail'])
        );
    }
}
