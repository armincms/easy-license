<?php

namespace Armincms\EasyLicense;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Nova\Nova as LaravelNova;
use Zareismail\Gutenberg\Gutenberg;

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
        // $generator = fn () => ['username' => 'username:'.time(), 'password' => 'password:'.time()];
        // config(['easylicense.operators' => [
        //     'eset' => [
        //         'label' => 'Eset',
        //         'drivers' => [
        //             'a' => ['label' => 'a', 'generator' => $generator],
        //             'b' => ['label' => 'b', 'generator' => $generator],
        //             'c' => ['label' => 'c', 'generator' => $generator],
        //         ],
        //     ],
        // ]]);

        $this->routes();
        $this->listeners();

        Gutenberg::widgets([
            \Armincms\EasyLicense\Cypress\Widgets\IndexLicense::class,
            \Armincms\EasyLicense\Cypress\Widgets\LicenseCheckout::class,
            \Armincms\EasyLicense\Cypress\Widgets\PurchaseCheckout::class,
        ]);

        Gutenberg::fragments([
            \Armincms\EasyLicense\Cypress\Fragments\LicenseCheckout::class,
            \Armincms\EasyLicense\Cypress\Fragments\PurchaseCheckout::class,
        ]);

        Gutenberg::templates([
            \Armincms\EasyLicense\Gutenberg\Templates\SingleLicense::class,
            \Armincms\EasyLicense\Gutenberg\Templates\LicenseCheckout::class,
            \Armincms\EasyLicense\Gutenberg\Templates\PurchaseCheckout::class,
        ]);
    }

    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Card::class,
            Nova\License::class,
            Nova\LicenseCheckout::class,
            Nova\Purchase::class,
        ]);
    }

    public function registerPolicies()
    {
        Gate::policy(License::class, Policies\License::class);
        Gate::policy(Card::class, Policies\Card::class);
    }

    public function routes()
    {
        $this->app['router']
            ->middleware('web')
            ->group(function ($router) {
                $router->post('/_el_/licenses/{licenseId}', Http\Controllers\PurchaseStoreController::class)->name('el.purchase.store');
                $router->post('/_el_/purchases/{number}', Http\Controllers\PurchaseSubmitController::class)->name('el.purchase.submit');
                $router->get('/_el_/purchases/{number}', Http\Controllers\PurchaseDiscardController::class)->name('el.purchase.discard');
                $router->any('/_el_/purchases/{number}/verified', Http\Controllers\PurchaseDoneController::class)->name('el.purchase.done');
            });
    }

    public function listeners()
    {
        \Event::listen(Events\PurchaseDone::class, Listeners\PurchaseDone::class);
    }
}
