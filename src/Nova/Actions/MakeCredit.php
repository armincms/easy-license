<?php

namespace Armincms\EasyLicense\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\{ActionFields, Text, Number};
use Armincms\EasyLicense\Nova\Manufacturer;
use Armincms\EasyLicense\Credit;

class MakeCredit extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    { 
        $license = $models->first();

        if($license->delivery !== 'system') { 
            return Action::push('/resources/el-credits/new', [
              'viaResource' => 'el-licenses',
              'viaResourceId' => $license->id,
              'viaRelationship' => 'credits'
            ]); 
        }

        $license->load(['product' => function($q) {
            $q->withTrashed()->with(['manufacturer' => function($q) {
                $q->withTrashed();
            }]);
        }]);

        if(is_null($manufacturer = optional($license->product)->manufacturer)) {
            abort(404);
        }

        $manufacturer = new Manufacturer($manufacturer);

        $builder = $manufacturer->drivers()->get($license->product->driver)['builder'] ?? function() {
          return [];
        };

        $credits = collect(range(1, $fields->get('count')))->map(function() use ($builder, $fields, $license) {
            return Credit::unguarded(function() use ($license, $builder, $fields) {
                return Credit::firstOrCreate([
                    'license_id' => $license->id,
                    'usage' => $fields->get('usage'),
                    'data' => collect($license->product->prepareFields())->flatMap(function($field) use ($builder) {
                        $name = $field['name'];

                        return [
                            $name => data_get($builder(), $name)
                        ];
                    })->toArray(),
                ]);
            });
        }); 

        return $credits->count() > 1
                    ? Action::push('/resources/el-credits/lens/made-credits', [
                        'el-credits_search' => $credits->map->getKey()->implode(',')
                    ]) 
                    : Action::push('/resources/el-credits/'. $credits->first()->id); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    { 
        return [
            Number::make(__('How many?'), 'count')
                ->min(1)
                ->max(10)
                ->required()
                ->rules('required')
                ->help(__('Number of credits required')),

            Text::make(__('Whats Usage'), 'usage')
                ->required()
                ->rules('required'),
        ];
    }
}
