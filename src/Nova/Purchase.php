<?php

namespace Armincms\EasyLicense\Nova;

use Armincms\Arminpay\Nova\Transaction;
use Armincms\Contract\Nova\Fields;
use Armincms\Contract\Nova\User;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphedByMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Purchase extends Resource
{
    use Fields;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\EasyLicense\Models\Purchase::class;

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
    public static $with = ['card.license', 'user'];

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
            BelongsTo::make(__('Purchase License'), 'license', License::class)
                ->withoutTrashed()
                ->placeholder(__('Select a license to sell'))
                ->filterable(),

            Select::make(__('Purchase Card'), 'card_id')
                ->required()
                ->rules('required')
                ->placeholder(__('Select a card to sell'))
                ->filterable()
                ->displayUsingLabels()
                ->dependsOn('license', function (Select $field, NovaRequest $request, FormData $formData) {
                    $field->options(Card::newModel()->where('license_id', $formData->integer('license'))->get()->keyBy->getKey()->mapInto(Card::class)->map->title());
                }),

            BelongsTo::make(__('Card Sold To'), 'user', User::class)
                ->withoutTrashed()
                ->nullable()
                ->filterable()
                ->showCreateRelationButton()
                ->placeholder(__('Not sold yet!')),

            Select::make(__('Purchase state'), 'marked_as')->options(static::purchaseStates())->filterable(),

            $this->currencyField(__('Purchase amount'), 'amount')
                ->help(__('Leave it empty to retrieve from the license.'))
                ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                    $model->{$attribute} = floatval($request->input($requestAttribute)) ?: License::newModel()->findOrFail($request->get('detail->license->id'))->finalPrice();
                }),

            Number::make(__('Purchase Count'), 'count')->filterable()->sortable()->required()->rules('required', 'min:1'),

            Textarea::make(__('User Note'), 'note')->nullable(),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForDetail(NovaRequest $request)
    {
        return [
            ID::make(),

            Text::make(__('Purchase Number'), fn () => $this->trackingCode())->filterable()->sortable(),

            Number::make(__('Purchase Count'), 'count')->filterable()->sortable(),

            Badge::make(__('Purchase state'), 'marked_as')
                ->labels(static::purchaseStates())
                ->map([
                    'pending' => 'warning',
                    'checkout' => 'info',
                    'paid' => 'info',
                    'delivery' => 'warning',
                    'failure' => 'warning',
                    'complete' => 'success',
                    'disacrd' => 'danger',
                ]),

            BelongsTo::make(__('Purchase License'), 'license', License::class)->sortable()->filterable(),

            $this->currencyField(__('Purcahse amount'), 'amount')->sortable()->filterable(),

            BelongsTo::make(__('Purchase User'), 'user', User::class)->sortable()->filterable(),

            BelongsTo::make(__('Purchase Detail'), 'card', Card::class)->sortable(),

            $this->datetimeField(__('Creation date'), 'created_at')->onlyOnDetail()->sortable(),

            MorphOne::make(__('Purchase Successed Transaction'), 'transaction', Transaction::class)->hideFromIndex(),

            MorphMany::make(__('Purchase Transactions'), 'transactions', Transaction::class),

            Panel::make(__('Purchase Detail'), [
                KeyValue::make(__('Purchase License'), fn () => (array) $this->detail('license')),

                KeyValue::make(__('Purchase User'), fn () => (array) $this->detail('user')),
            ])
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForIndex(NovaRequest $request)
    {
        return $this->fieldsForDetail($request);
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->with([
            'user',
            'card',
            'license',
        ]);
    }

    /**
     * Get the purchase states.
     *
     * @return array
     */
    public static function purchaseStates(): array
    {
        return [
            'pending' => __('Pending'),
            'checkout' => __('Wait to payment'),
            'paid' => __('Paid'),
            'delivery' => __('Delivery'), // paid without delivery
            'failure' => __('Failure'), // paid and delivery failed
            'complete' => __('Completed'), // paid and card delivered
            'disacrd' => __('Discard'),
        ];
    }
}
