<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Select, Boolean, KeyValue, HasMany};
use Armincms\Fields\Targomaan;

class Manufacturer extends Resource
{ 
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\Manufacturer'; 

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {    
    	return [ 
            ID::make()->sortable(),

			Select::make(__('Operator'), 'operator')
				->options($this->operators()->map->title->all())
				->required()
                ->sortable()
                ->rules('required'),

            Boolean::make(__('Active'), 'marked_as')
                ->default(0)
                ->sortable(),

    		new Targomaan([ 
    			Text::make(__('Name'), 'name')
    				->required(), 

                KeyValue::make(__('Products Features'), 'features')
                    ->keyLabel(__('Feature'))
                    ->valueLabel(__('Description'))
                    ->actionText(__('Append A New Feature')),

                $this->abstractField(),

                // $this->gutenbergField(),
    		]),

            new Panel(__('Other'), [ 
                $this->imageField(), 

                new Targomaan([ 
                    $this->seoField(),
                ]),
            ]),

            HasMany::make(__('Products'), 'products', Product::class),
    	];
    }

    public function drivers()
    {
        return collect(data_get($this->operator(), 'drivers', [])); 
    }

    public function operator()
    {
        return static::operators()->get($this->operator);
    }

    public function operatorsInformation()
    {
        return static::operators()->map(function($operator, $key) { 
            return $operator['title'] ?? $key;
        })->all();
    }

	public static function operators()
	{
		return collect(config('licence-management.operators'));
	} 
}

		