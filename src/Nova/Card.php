<?php

namespace Armincms\EasyLicense\Nova;

use Armincms\Contract\Nova\Fields;
use Armincms\Contract\Nova\User;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Card extends Resource
{
    use Fields;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\EasyLicense\Models\Card::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'number';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = ['license'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'number', 'config',
    ];

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

            Text::make(__('Card Number'), 'number')
                ->exceptOnForms()
                ->filterable()
                ->sortable()
                ->showWhenPeeking(),

            BelongsTo::make(__('Card License'), 'license', License::class)
                ->withoutTrashed()
                ->filterable()
                ->sortable()
                ->required()
                ->rules('required')
                ->inverse('cards')
                ->placeholder(__('Select a license to sell')),

            BelongsTo::make(__('Card Sold To'), 'user', User::class)
                ->withoutTrashed()
                ->filterable()
                ->sortable()
                ->nullable()
                ->showCreateRelationButton()
                ->placeholder(__('Not sold yet!')),

            KeyValue::make(__('License Information'), 'config->information')
                ->disableEditingKeys()
                ->hideWhenUpdating()
                ->dependsOn('license', function (KeyValue $field, NovaRequest $request, FormData $formData) {
                    if (! empty((array) $this->config('information'))) {
                        return;
                    }

                    $license = License::newModel()->find($formData->license) ?? $request->findParentModel();

                    if (is_null($license)) {
                        return $field->hide();
                    }

                    $data = $license->isAutomate() ? $license->generateCardInformation() : [];

                    $field->show();

                    $field->value = collect($license->config('fields'))->merge($data)->map(fn ($value, $key) => $data[$key] ?? null)->toArray();
                }),

            Textarea::make(__('User Note'), 'note')->nullable(),

            Boolean::make(__('Card is enable'), 'enable')->default(false)->filterable()->sortable(),
            Boolean::make(__('Card is available'), fn () => $this->isAvailable()),

            $this->datetimeField(__('Creation date'), 'created_at')->onlyOnDetail()->showWhenPeeking(),
            $this->datetimeField(__('Activation date'), 'activated_at')->exceptOnForms()->showWhenPeeking(),
            $this->datetimeField(__('Expiration date'), 'expires_on')->showWhenPeeking(),
            $this->datetimeField(__('Sale date'), 'sold_at')->showWhenPeeking(),
        ];
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
}
