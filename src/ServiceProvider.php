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
        $generator = fn () => ['username' => 'username:'.time(), 'password' => 'password:'.time()];
        config(['easylicense.operators' => [
            'eset' => [
                'label' => 'Eset',
                'drivers' => [
                    'a' => ['label' => 'a', 'generator' => $generator],
                    'b' => ['label' => 'b', 'generator' => $generator],
                    'c' => ['label' => 'c', 'generator' => $generator],
                ],
            ],
        ]]);

        Gutenberg::widgets([
            \Armincms\EasyLicense\Cypress\Widgets\IndexLicense::class,
        ]);

        Gutenberg::templates([
            \Armincms\EasyLicense\Gutenberg\Templates\SingleLicense::class,
        ]);
    }

    public function servingNova()
    {
        LaravelNova::resources([
            Nova\Card::class,
            Nova\License::class,
        ]);
    }

    public function registerPolicies()
    {
        Gate::policy(License::class, Policies\License::class);
        Gate::policy(Card::class, Policies\Card::class);
    }
}
