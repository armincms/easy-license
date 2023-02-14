<?php

namespace Armincms\EasyLicense\Cypress\Widgets;

use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\EasyLicense\Cypress\Fragments\PurchaseCheckout;
use Laravel\Nova\Fields\Select;
use Zareismail\Gutenberg\Gutenberg;
use Zareismail\Gutenberg\GutenbergWidget;

class LicenseCheckout extends GutenbergWidget
{
    /**
     * The logical group associated with the template.
     *
     * @var string
     */
    public static $group = 'License';

    /**
     * Indicates if the widget should be shown on the component page.
     *
     * @var \Closure|bool
     */
    public $showOnComponent = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function fields($request)
    {
        return [
            Select::make(__('Purchase Checkout Page'), 'config->purchaseCheckout')
                ->options(Gutenberg::cachedFragments()->forHandler(PurchaseCheckout::class)->keyBy->getKey()->map->name)
                ->displayUsingLabels()
                ->required()
                ->rules('required'),
        ];
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $license = $this->getRequest()->resolveFragment()->resource();
        $errors = [];

        if (session()->has('errors')) {
            $errors = collect(session('errors')->messages())->map(fn ($messages) => $messages[0])->toArray();
        }

        return array_merge($license->serializeForDetailWidget($this->getRequest()), [
            'method' => 'POST',
            'csrf_field' => csrf_field(),
            'csrf_token' => csrf_token(),
            'action' => route('el.purchase.store', [$license->getKey(), 'purchaseCheckout' => $this->metaValue('purchaseCheckout')]),
            'gates' => ArminpayGateway::enable()->get()->map->serializeForWidget($this->getRequest())->toArray(),
            'form' => [
                'email' => old('email'),
                'username' => old('username'),
                'firstname' => old('firstname'),
                'lastname' => old('lastname'),
                'phone' => old('phone'),
                'mobile' => old('mobile'),
                'count' => old('count', 1),
            ],
            'errors' => $errors,
        ]);
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
        $query->handledBy(\Armincms\EasyLicense\Gutenberg\Templates\LicenseCheckout::class);
    }
}
