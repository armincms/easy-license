<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Password, Select, Boolean, BooleanGroup, BelongsTo, HasMany};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\{Targomaan, Chain};
use Whitecube\NovaFlexibleContent\Flexible;

class Product extends Resource
{ 
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\Product'; 

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['manufacturer'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {    
    	return [ 

            Chain::as('manufacturer', function() {
                return [ 
                    Select::make(__('Manufacturer'), 'manufacturer_id', ) 
                        ->options(Manufacturer::newModel()->get()->keyBy->getKey()->map->name)
                        ->required()
                        ->rules('required'),
                ];
            }),

            Chain::with('manufacturer', function($request) {
                $manufacturer = new Manufacturer(
                    Manufacturer::newModel()->findOrNew($request->get('manufacturer_id'))
                );
 
                $drivers = collect($manufacturer->drivers())->map(function($driver, $name) {
                    return $driver['title'] ?? $name;
                }); 

                return with([ 
                    Select::make(__('Driver'), 'driver') 
                        ->options($drivers)
                        ->required()
                        ->rules('required'),
                ], function($fields) use ($manufacturer) { 

                    if(! empty($manufacturer->features)) {
                        return array_merge($fields, [ 
                            BooleanGroup::make(__('Features'), 'features') 
                                ->options(collect($manufacturer->features)->map(function($description, $label) {
                                    return empty($description) ? $label : $description;
                                })->all())
                                ->required()
                                ->rules('required'),
                        ]);
                    }

                    return $fields;

                }); 
            }),  			
        
            Boolean::make(__('Active'), 'marked_as')
                ->default(0),

    		new Targomaan([
    			Text::make(__('Name'), 'name')
    				->required(), 

    			$this->abstractField(),   
    		]),  

            BooleanGroup::make(__('Features'), 'features') 
                ->options(collect(optional($this->manufacturer)->features)->map(function($description, $label) {
                    return empty($description) ? $label : $description;
                })->all())
                ->onlyOnDetail(),  

            Flexible::make(__('Required Data'), 'fields')
                ->addLayout(__('Number'), Number::class, [
                    Text::make(__('Name'), 'name')
                        ->required()
                        ->rules('required'),

                    Number::make(__('Minimum'), 'min'),

                    Number::make(__('Maximum'), 'max'),

                    Boolean::make(__('Required'), 'required'),
                ])
                ->addLayout(__('Text'), Text::class, [
                    Text::make(__('Name'), 'name')
                        ->required()
                        ->rules('required'), 

                    Boolean::make(__('Required'), 'required'),
                ])
                ->addLayout(__('Password'), Password::class, [
                    Text::make(__('Name'), 'name')
                        ->required()
                        ->rules('required'), 

                    Boolean::make(__('Required'), 'required'),
                ])
                ->addLayout(__('Select'), Select::class, [
                    Text::make(__('Name'), 'name')
                        ->required()
                        ->rules('required'), 

                    $this->tagsInput(__('Options'), 'options'),

                    Boolean::make(__('Required'), 'required'),
                ])
                ->button(__('Add Field'))
                ->collapsed(false),

    		new Panel(__('Other'), [
                    $this->imageField(),
    		]),

            HasMany::make(__('Licenses'), 'licenses', License::class),
    	];
    }

	public function operators()
	{
		return collect(config('licence-management.operators'))->map(function($operator, $key) { 
			return $operator['title'] ?? $key;
		})->all();
	} 

    public function fieldsHistory()
    {
        return static::newModel()->get()->flatMap->fields->values()->merge([
            'username', 'password', 'key', 'secret'
        ])->unique()->toArray();
    }
}

		