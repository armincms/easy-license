<?php

namespace Armincms\EasyLicense\Cypress\Widgets;

use Armincms\Categorizable\Nova\Category;
use Armincms\Duration\Nova\Duration;
use Armincms\EasyLicense\Nova\License;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Zareismail\Gutenberg\GutenbergWidget;

class IndexLicense extends GutenbergWidget
{
    /**
     * The logical group associated with the template.
     *
     * @var string
     */
    public static $group = 'License';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function fields($request)
    {
        return [
            Number::make(__('License display limit'), 'config->count')
                ->nullable()
                ->min(1)
                ->required()
                ->rules('min:1', 'required', 'numeric')
                ->default(9),

            Number::make(__('License users'), 'config->users')->nullable()->min(0),

            MultiSelect::make(__('Filter by categories'), 'config->categories')
                ->options(Category::newModel()->get()->keyBy->getKey()->mapInto(Category::class)->map->title())
                ->placeholder(__('Do not filter by categories'))
                ->nullable(),

            Select::make(__('Filter by duration'), 'config->duration')
                ->options(Duration::newModel()->get()->keyBy->getKey()->mapInto(Duration::class)->map->title())
                ->placeholder(__('Do not filter by duration'))
                ->nullable(),

            Select::make(__('Filter by operator'), 'config->operator')
                ->options(License::operators())
                ->placeholder(__('Do not filter by operator'))
                ->nullable(),

            MultiSelect::make(__('Filter by driver'), 'config->driver')
                ->placeholder(__('Do not filter by driver'))
                ->nullable()
                ->hide()
                ->dependsOn('config->operator', function (MultiSelect $field, NovaRequest $request, FormData $formData) {
                    if ($formData->string('config->operator')) {
                        $field->show()->options(License::drivers($formData->string('config->operator')));
                    } else {
                        $field->hide()->options([]);
                    }
                }),

            MultiSelect::make(__('Filter by delivery'), 'config->delivery')
                ->options(License::deliveryMethods())
                ->placeholder(__('Do not filter by delivery'))
                ->nullable(),
        ];
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'resources' => $this->filterLicenses()->map->serializeForWidget($this->getRequest()),
        ];
    }

    /**
     * Get licesnse for given filter on widget.
     *
     * @return \Illuminate\Support\Collection
     */
    public function filterLicenses()
    {
        $categories = collect($this->metaValue('categories'))->filter()->toArray();
        $drivers = collect($this->metaValue('driver'))->filter()->toArray();
        $deliveries = collect($this->metaValue('delivery'))->filter()->toArray();

        return License::newModel()
            ->with(['media', 'duration'])
            ->limit((int) $this->metaValue('count'))
            ->enables()
            ->when((int) $this->metaValue('users'), fn ($query) => $query->whereUsers($this->metaValue('users')))
            ->when($categories, function ($query) use ($categories) {
                $query->whereHas(
                    'categories',
                    fn ($query) => $query->whereKey(Category::newModel()->findMany($categories)->toFlatTree()->modelKeys())
                );
            })
            ->when((int) $this->metaValue('duration'), fn ($query) => $query->whereHas('duration', fn ($query) => $query->whereKey($this->metaValue('duration'))))
            ->when((int) $this->metaValue('operator'), fn ($query) => $query->where('config->operator', $this->metaValue('operator')))
            ->when($drivers, fn ($query) => $query->whereIn('config->driver', $drivers))
            ->when($deliveries, fn ($query) => $query->whereIn('delivery', $deliveries))
            ->get();
    }

    /**
     * Query related dispaly templates.
     *
     * @param    $request
     * @param    $query
     * @return
     */
    public static function relatableTemplates($request, $query)
    {
        $query->handledBy(\Armincms\EasyLicense\Gutenberg\Templates\SingleLicense::class);
    }
}
