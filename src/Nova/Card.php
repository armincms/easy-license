<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Select, Boolean, BelongsTo, HasMany};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\Targomaan; 

class Card extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\Card'; 

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['manuals'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {    
    	return [   
            Select::make(__('Product'), 'product')  
                ->options(Product::newModel()->get()->pluck('name', 'id')->all())
                ->required() 
                ->onlyOnForms()
                ->readonly($this->manuals->isNotEmpty())
                ->fillUsing(function() {})
                ->resolveUsing(function($value, $model, $attribute) {
                    return optional($model->load('license')->license)->product_id;
                }),

            BelongsTo::make(__('License'), 'license', License::class)
                ->exceptOnForms()
                ->showOnUpdating($this->manuals->isNotEmpty())
                ->readonly($this->manuals->isNotEmpty()),

            AjaxSelect::make(__('License'), 'license_id')
                ->get('/nova-api/ajax-selection/{product}/licenses')
                ->parent('product') 
                ->required()
                ->rules('required')
                ->onlyOnForms() 
                ->hideWhenUpdating($this->manuals->isNotEmpty()), 

    		new Targomaan([
    			Text::make(__('Name'), 'name')
    				->required(),
    		]), 

            Boolean::make(__('Active'), 'marked_as')
                ->default(0), 

            HasMany::make(__('Licenses'), 'manuals', Manual::class),
    	];
    }  
}

		
