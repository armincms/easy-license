<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Select, Boolean, BelongsTo, HasMany};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\Targomaan;
use Epartment\NovaDependencyContainer\HasDependencies;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;

class License extends Resource
{ 
    use HasDependencies;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\License'; 

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {    
    	return [ 
            new Targomaan([
                Text::make(__('Name'), 'name')
                    ->exceptOnForms(),

                Text::make(__('Abstract'), 'abstract')
                    ->onlyOnDetail(),
            ]),

			BelongsTo::make(__('Product'), 'product', Product::class) 
				->withoutTrashed()
				->required()
				->rules('required'),

            BelongsTo::make(__('Duration'), 'duration', Duration::class) 
                ->withoutTrashed()
                ->required()
                ->rules('required'),

			Select::make(__('Delivery Method'), 'delivery') 
				->options(static::deliveryMethods()) 
                ->displayUsingLabels()
				->required()
				->rules('required')
                ->default('system'),


            Number::make(__('Users'), 'users')
                ->min(1)
                ->max(1000)
                ->required()
                ->rules('required')
                ->default(1),

            $this->priceField('Price', 'price', EasyLicense::option('el_currency', 'IRR')),

            Text::make(__('Discount'), function() {
                return $this->discountPrice().PHP_EOL.EasyLicense::option('el_currency', 'IRR'); 
            }),

            Select::make(__('Discount'), 'discount->type') 
                ->options(static::discounts())   
                ->nullable()
                ->onlyOnForms(),

            NovaDependencyContainer::make([
                $this
                    ->priceField(__('Discount Amount'), 'discount->value') 
                    ->onlyOnForms(),
            ])->dependsOn('discount->type', 'amount'), 

            NovaDependencyContainer::make([
                Number::make(__('Discount Percent'), 'discount->value')
                    ->default(1)
                    ->min(0)
                    ->onlyOnForms(),
            ])->dependsOn('discount->type', 'percent'), 

            Boolean::make(__('Active'), 'marked_as')
                ->default(0),

    		new Targomaan([

    			Text::make(__('Name'), 'name')
    				->required()
                    ->onlyOnForms()
                    ->rules('required'), 

    			$this->abstractField()->onlyOnForms(),  
    		]),   

            new Panel(__('Other'), [
                $this->imageField()->hideFromIndex(),
            ]), 

            HasMany::make(__('Credits'), 'credits', Credit::class),
    	];
    }  

    /**
     * Get the actions available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            (new Actions\MakeCredit) 
                ->canRun(function($request) {
                    return $request->user()->can('addCredit', $request->resource());
                })
                ->exceptOnIndex(), 
        ];
    }

    /**
     * Get the delivery methods
     * 
     * @return array
     */
	public static function deliveryMethods()
	{
		return [
            'manual' => __('Manual'), 
            'system' => __('System'), 
            'card'   => __('Card'),
        ];
	}

    /**
     * Get the discounts
     * 
     * @return array
     */
    public static function discounts()
    {
        return [ 
            'percent'   => __('Percent'), 
            'amount'    => __('Amount'),
        ];
    }  
}

		