<?php

namespace Armincms\EasyLicense\Gutenberg\Templates;

use Zareismail\Gutenberg\Template;
use Zareismail\Gutenberg\Variable;

class PurchaseCheckout extends Template
{
    /**
     * The logical group associated with the template.
     *
     * @var string
     */
    public static $group = 'License';

    /**
     * Register the given variables.
     *
     * @return array
     */
    public static function variables(): array
    {
        return [
            Variable::make('number', __('Purchase Number')),

            Variable::make('count', __('License purchase count')),

            Variable::make('name', __('License Name')),

            Variable::make('amount', __('Purchase total amount')),

            Variable::make('price', __('License price after discounting')),

            Variable::make('finalPrice', __('License price after discounting')),

            Variable::make('originalPrice', __('License real price')),

            Variable::make('discountAmount', __('License discount amount')),

            Variable::make('discountPercent', __('License discount percentage')),

            Variable::make('delivery', __('License Delivery Method')),

            Variable::make('users', __('License valid user count')),

            Variable::make('card', __('License sold card detail')),

            Variable::make('transaction', __('License successed purchase transaction [trackingCode,referenceNumber,state,date]')),
            Variable::make('transactions', __('License purchase transactions [trackingCode,referenceNumber,state,date]')),

            Variable::make('gates', __('Purchase gateways([{ name, id, image.[common-main|common-thumbnail] }])')),

            Variable::make('image.templateName', __(
                'Image with the required template (example: image.common-main)'
            )),
        ];
    }
}
