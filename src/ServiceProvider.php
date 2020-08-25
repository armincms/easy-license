<?php

namespace Armincms\EasyLicense;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider; 
use Laravel\Nova\Nova as LaravelNova;
use Armincms\EasyLicense\Http\Middleware\Authorize; 
use Illuminate\Support\Facades\Gate;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    { 
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');  

        LaravelNova::serving([$this, 'servingNova']);

        $this->registerPolicies();

        app('router')->group([
            'middleware' => 'nova',
            'namespace'  => __NAMESPACE__.'\Http\Controllers',
            'prefix'     => 'ajax-selection'
        ], function($router) {
            $router->get('{manufacturer}/drivers', 'ManufacturerController@handle');
            $router->get('/{product}/licenses', 'LicenseController@handle'); 
        }); 
    }

    public function servingNova()
    {
        LaravelNova::resources([
            Nova\EasyLicense::class,
            Nova\Manufacturer::class,
            Nova\Duration::class,
            Nova\Product::class,
            Nova\License::class,
            Nova\Credit::class,
            Nova\Manual::class,
            Nova\Card::class,
        ]); 
    } 

    public function registerPolicies()
    { 
        Gate::policy(Manufacturer::class, Policies\EasyLicenseManufacturer::class);
        Gate::policy(Product::class, Policies\EasyLicenseProduct::class);
        Gate::policy(License::class, Policies\EasyLicenseLicense::class);
        Gate::policy(Credit::class, Policies\EasyLicenseCredit::class);
        Gate::policy(Manual::class, Policies\EasyLicenseCardLicense::class);
        Gate::policy(Card::class, Policies\EasyLicenseCard::class);
    }
}
