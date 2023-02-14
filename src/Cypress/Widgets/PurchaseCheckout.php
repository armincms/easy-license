<?php

namespace Armincms\EasyLicense\Cypress\Widgets;

use Armincms\Arminpay\Models\ArminpayGateway;
use Zareismail\Gutenberg\GutenbergWidget;

class PurchaseCheckout extends GutenbergWidget
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
        ];
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $purhcase = $this->getRequest()->resolveFragment()->resource()->load('license', 'user', 'card', 'transaction', 'transactions');
        $errors = [];

        if (session()->has('errors')) {
            $errors = collect(session('errors')->messages())->map(fn ($messages) => $messages[0])->toArray();
        }

        return array_merge($purhcase->serializeForDetailWidget($this->getRequest()), compact('errors'), [
            'method' => 'POST',
            'csrf_field' => csrf_field(),
            'csrf_token' => csrf_token(),
            'action' => route('el.purchase.submit', $purhcase->trackingCode()),
            'successed' => ! in_array($purhcase->marked_as, ['pending', 'checkout']),
            'gates' => ArminpayGateway::enable()->get()->map->serializeForWidget($this->getRequest())->toArray(),
            'errors' => $errors,
            'card' => (array) optional($purhcase->card)->config('information'),
            'transaction' => optional($purhcase->transaction)->serializeForDetailWidget($this->getRequest()),
            'transactions' => $purhcase->transactions->map->serializeForDetailWidget($this->getRequest())->toArray(),
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
        $query->handledBy(\Armincms\EasyLicense\Gutenberg\Templates\PurchaseCheckout::class);
    }
}
