<?php

namespace Armincms\EasyLicense\Nova;

use Armincms\Contract\Nova\Option;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class LicenseCheckout extends Option
{
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Boolean::make(__('Email is requried'), 'email')->default(true),
            Boolean::make(__('Username is requried'), 'username')->default(false),
            Boolean::make(__('Firstname is requried'), 'firsname')->default(false),
            Boolean::make(__('Lastname is requried'), 'lastname')->default(false),
            Boolean::make(__('Mobile is requried'), 'mobile')->default(false),
            Boolean::make(__('Phone is requried'), 'phone')->default(false),
        ];
    }
}
