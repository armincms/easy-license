<?php

namespace Armincms\EasyLicense\Gutenberg\Templates;

use Zareismail\Gutenberg\Template;
use Zareismail\Gutenberg\Variable;

class SingleLicense extends Template
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

            Variable::make('purchaseUrl', __('License purchase link')),

            Variable::make('image.templateName', __(
                'Image with the required template (example: image.common-main)'
            )),
        ];
    }
}
