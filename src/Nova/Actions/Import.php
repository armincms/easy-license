<?php

namespace Armincms\EasyLicense\Nova\Actions;

use Armincms\EasyLicense\Manual;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;

class Import extends Action
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
        $cardId = $models->map->getKey()->pop();
        $items = fastexcel()->import($fields->get('file')) 
            ->map(function($data) use ($cardId) {
                return [
                    'data' => json_encode($data),
                    'created_at' => (string) now(),
                    'updated_at' => (string) now(),
                    'card_id' => $cardId
                ];
            });
      
        Manual::insert($items->toArray()); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            File::make(__('Upload your file'), 'file')
                ->required()
                ->rules('required', 'mimes:xlsx')
                ->acceptedTypes('.xlsx')
                ->help(__('Only excel files accepted(.xlsx)'))
        ];
    }
}
