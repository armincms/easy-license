<?php 

namespace Armincms\EasyLicense\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\{ID, Text, Number, Select, Boolean, DateTime, BelongsTo};
use NovaAjaxSelect\AjaxSelect;
use Armincms\Fields\Targomaan; 
use Laravel\Nova\Http\Requests\NovaRequest;

class Manual extends Resource
{  
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\EasyLicense\\Manual'; 

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['card'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'data',
    ];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 15;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {     
        $card = optional($this->resource)->card 
                    ?? Card::newModel()->findOrFail($request->get('viaResourceId')); 

        $card->load(['license' => function($q) {
            $q->withTrashed()->with(['product' => function($q) {
                $q->withTrashed();
            }]);
        }]);

        if(is_null($product = optional($card->license)->product)) {
            abort(404);
        }

        return $product
                    ->prepareFields()
                    ->map(function($attributes) {
                        return with($attributes['field'], function($field) use ($attributes) {
                            return $field::make($attributes['name'])
                                        ->required($required = $attributes['required'])
                                        ->rules($required ? 'required' : [])
                                        ->hideFromIndex()
                                        ->fillUsing(function($request, $model, $attribute, $requestAttribute) use ($attributes) {
                                            $model->setAttribute("data->{$attributes['name']}", $request->get($requestAttribute));
                                        })
                                        ->resolveUsing(function($request, $model, $attribute) use ($attributes) { 
                                            return data_get($model->data, $attributes['name']);
                                        });
                        });
                    })
                    ->prepend(ID::make(__('ID'), 'id'))
                    ->merge([
                        Boolean::make(__('Sold'), 'sold')
                            ->default(0)
                            ->sortable()
                            ->exceptOnForms(),

                        DateTime::make(__('Sold At'), 'sold_at')
                            ->exceptOnForms(),
                    ])
                    ->all(); 
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
            (new Actions\MarkAsSold)->canSee(function ($request) { 
                if($resource = $this->resource) {
                    return $request->user()->can('sell', $resource) && $resource->forSale();
                } 
            })->onlyOnTableRow(),
        ];
    }
}

        