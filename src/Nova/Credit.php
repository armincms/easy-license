<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Select, Boolean, BelongsTo, DateTime};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\Targomaan; 

class Credit extends Resource
{  
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\Credit'; 

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['license.product'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'data',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {     
        $product = data_get($this->resource, 'license.product');

        $fields = optional($product)->prepareFields();

        return [
            ID::make()->sortable(),

            BelongsTo::make(__('License'), 'license', License::class)
                ->sortable()
                ->withoutTrashed(),

            Text::make(__('Usage'), 'usage')
                ->sortable(),

            DateTime::make(__('Expires On'), 'expires_on')->sortable(),

            DateTime::make(__('Created At'), 'created_at')
                ->readonly()
                ->sortable(),

            new Panel(__('Data'), collect($fields)->pluck('field', 'name')->map(function($field, $name) {    
                $field = class_exists($field) ? $field : Text::class;

                return $this->when(
                    boolval(request()->get('viaResourceId')), $field::make($name, "data->{$name}")
                );
            })->all())
        ];
    } 


    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    { 
        return false;
    }  

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    { 
        return false;
    }  

    /**
     * Get the lenses available on the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            (new Lenses\MadeCredits)->canSee(function($request) {
                return $request->route('lens');
            })
        ];
    }
}

		