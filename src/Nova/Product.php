<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Password, Select, Boolean, BelongsTo};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\Targomaan;
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {    
    	return [ 
			BelongsTo::make(__('Manufacturer'), 'manufacturer', Manufacturer::class) 
				->withoutTrashed()
				->required()
				->rules('required'),

			AjaxSelect::make(__('Driver'), 'driver')
    			->get('/ajax-selection/{manufacturer}/drivers')
				->parent('manufacturer') 
				->required()
				->rules('required'),
        
            Boolean::make(__('Active'), 'marked_as')
                ->default(0),

    		new Targomaan([
    			Text::make(__('Name'), 'name')
    				->required(), 

    			$this->abstractField(),   
    		]), 

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

		