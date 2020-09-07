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
    public static $with = ['license'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {     
        $product = data_get($this->resource->withDuration(), 'license.product');

        $fields = optional($product)->prepareFields();

        return [
            ID::make()->sortable(),

            BelongsTo::make(__('License'), 'license', License::class)
                ->sortable()
                ->withoutTrashed(),

            DateTime::make(__('Expires On'), 'expires_on')->sortable(),

            DateTime::make(__('Created At'), 'created_at')
                ->readonly()
                ->sortable(),

            new Panel(__('Data'), collect($fields)->map(function($attributes, $field) {    
                $field = class_exists($field) ? $field : Text::class;

                return $field::make($attributes['name'], "data->{$attributes['name']}");
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
}

		