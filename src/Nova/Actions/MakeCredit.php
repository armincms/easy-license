<?php

namespace Armincms\EasyLicense\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
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

        $builder = $manufacturer->drivers()->get($license->product->driver)['builder'];

        $data = []; //$builder::build()

        $credit = Credit::unguarded(function() use ($license, $data) {
            return Credit::firstOrCreate([
                'license_id' => $license->id,
                'data' => collect($license->product->prepareFields())->flatMap(function($field) use ($data) {
                    $name = $field['name'];

                    return [
                        $name => data_get($data, $name)
                    ];
                })->toJson(),
            ]);
        });

        return Action::push('/resources/el-credits/'. $credit->id); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    { 
        return [];
    }
}
