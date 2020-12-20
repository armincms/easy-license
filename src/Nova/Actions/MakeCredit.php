<?php

namespace Armincms\EasyLicense\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\{ActionFields, Text, Number};

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

        $credits = $license->createCredit(
          request()->user(), $fields->get('usage'), intval($fields->get('count')) ?: 1
        );

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
