<?php

namespace Armincms\EasyLicense\Nova;

use Armincms\Categorizable\Nova\Category;
use Armincms\Contract\Nova\Fields;
use Armincms\Duration\Nova\Duration;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class License extends Resource
{
    use Fields;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\EasyLicense\Models\License::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(),

            Text::make(__('Licesnse Name'), fn () => $this->title())->showWhenPeeking(),

            BelongsTo::make(__('License Duration'), 'duration', Duration::class)
                ->withoutTrashed()
                ->filterable()
                ->sortable()
                ->required()
                ->rules('required')
                ->showCreateRelationButton(),

            Select::make(__('Delivery Method'), 'delivery')
                ->options(static::deliveryMethods())
                ->displayUsingLabels()
                ->filterable()
                ->sortable()
                ->required()
                ->rules('required')
                ->default('manual')
                ->showWhenPeeking(),

            Select::make(__('License Operator'), 'config->operator')
                ->options(static::operators())
                ->filterable()
                ->sortable()
                ->displayUsingLabels()
                ->showOnDetail($this->delivery === 'system')
                ->hideFromIndex()
                ->dependsOn('delivery', function (Select $field, NovaRequest $request, FormData $formData) {
                    if ((string) $formData->get('delivery') !== 'system') {
                        $field->hide();
                    } else {
                        $field->show()->required()->rules('required');
                    }
                }),

            Select::make(__('License Builder'), 'config->driver')
                ->displayUsingLabels()
                ->filterable()
                ->sortable()
                ->showOnDetail($this->delivery === 'system')
                ->hideFromIndex()
                ->dependsOn(['delivery', 'config->operator'], function (Select $field, NovaRequest $request, FormData $formData) {
                    $operator = (string) $formData->get('config->operator');

                    if ((string) $formData->get('delivery') !== 'system' || empty($operator)) {
                        $field->hide();
                    } else {
                        $field->show()->required()->rules('required')->options(static::drivers($operator));
                    }
                }),

            KeyValue::make(__('License Reqruied Information'), 'config->fields')
                ->keyLabel(__('License Key Name'))
                ->valueLabel(__('License Key Description'))
                ->required()->rules('required'),

            ...collect(app('application.locales'))->flatMap(function ($locale) {
                return [
                    Text::make(__("License Name - [{$locale['name']}]"), "name->{$locale['locale']}")
                        ->required()
                        ->onlyOnForms()
                        ->help($locale['name']),
                ];
            }),

            Tag::make(__('Categoires'), 'categories', Category::class)->showCreateRelationButton()->required()->rules('required'),

            Number::make(__('Users'), 'users')
                ->filterable()
                ->sortable()
                ->min(1)
                ->max(1000)
                ->required()
                ->rules('required', 'numeric', 'min:1', 'max:1000')
                ->default(1)
                ->showWhenPeeking(),

            $this->currencyField(__('Price'))
                ->required()
                ->rules('required')
                ->filterable()
                ->sortable()
                ->showWhenPeeking(),

            Boolean::make(__('License is enable'), 'enable')
                ->default(false)
                ->filterable()
                ->sortable()
                ->showWhenPeeking(),

            $this->medialibrary(__('License Image'))->hideFromIndex(),

            HasMany::make(__('License Cards'), 'cards', Card::class),
        ];
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->resource->title();
    }

    /**
     * Get the delivery methods
     *
     * @return array
     */
    public static function deliveryMethods(): array
    {
        return [
            'manual' => __('Manual'),
            'system' => __('System'),
            'card' => __('Card'),
        ];
    }

    /**
     * Get the system delivery operators.
     *
     * @return array
     */
    public static function operators(): array
    {
        return collect(static::config('operators'))->map->label->toArray();
    }

    /**
     * Get the system operator drivers.
     *
     * @return array
     */
    public static function drivers(string $operator): array
    {
        return  collect(static::config("operators.{$operator}.drivers"))->map->label->toArray();
    }

    /**
     * Get driver configurations.
     *
     * @return array
     */
    public static function driver(string $operator, string $driver): array
    {
        return (array) data_get(static::operator($operator), "drivers.{$driver}", []);
    }

    /**
     * Get operator configurations.
     *
     * @return array
     */
    public static function operator(string $operator): array
    {
        return (array) static::config("operators.{$operator}", []);
    }

    /**
     * Get user configurations.
     *
     * @return array
     */
    public static function config(?string $key = null, $default = null)
    {
        return config(trim("easylicense.{$key}", '.'), $default);
    }
}
