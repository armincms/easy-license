<?php 

namespace Armincms\EasyLicense\Nova\Panels;
 
use Laravel\Nova\Fields\Text; 
use Laravel\Nova\Panel; 
use Laravel\Nova\Fields\Heading; 

class OrderDetail extends Panel
{ 
    /**
     * The resource instance.
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $resource;

    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct($name, $resource)
    {
        $this->resource = $resource;

        return parent::__construct($name, $this->fields());
    }

    public function fields()
    {
        return collect($this->resource->saleables)->flatMap(function($saleable) {
            return collect($saleable->details)->map->data->flatMap(function($data) {
                return collect($data)->map(function($value, $key) {
                    return Text::make($key)->withMeta(compact('value'))->onlyOnDetail();
                });
            })->values()->prepend(Heading::make($saleable->name)->onlyOnDetail()); 
        });
    }
}
		