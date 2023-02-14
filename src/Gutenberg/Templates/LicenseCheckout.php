<?php

namespace Armincms\EasyLicense\Gutenberg\Templates;

use Zareismail\Gutenberg\Template;
use Zareismail\Gutenberg\Variable;

class LicenseCheckout extends Template
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
            Variable::make('id', __('License Id')),

            Variable::make('name', __('License Name')),

            Variable::make('price', __('License price after discounting')),

            Variable::make('finalPrice', __('License price after discounting')),

            Variable::make('originalPrice', __('License real price')),

            Variable::make('discountAmount', __('License discount amount')),

            Variable::make('discountPercent', __('License discount percentage')),

            Variable::make('delivery', __('License Delivery Method')),

            Variable::make('users', __('License valid user count')),

            Variable::make('gates', __('Purchase gateways([{ name, id, image.[common-main|common-thumbnail] }])')),

            Variable::make('image.templateName', __(
                'Image with the required template (example: image.common-main)'
            )),

            Variable::make('form.username', __('License user username')),
            Variable::make('form.firstname', __('License user firstname')),
            Variable::make('form.lastname', __('License user lastname')),
            Variable::make('form.email', __('License user email')),
            Variable::make('form.phone', __('License user phone')),
            Variable::make('form.mobile', __('License user mobile')),
            Variable::make('form.count', __('License purchase number')),
        ];
    }
}
