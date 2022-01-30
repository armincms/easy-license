<?php

namespace Armincms\EasyLicense;
 
use Illuminate\Support\ServiceProvider as LaravelServiceProvider; 
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Laravel\Nova\Nova as LaravelNova; 
use Armincms\Orderable\Events\OrderCompleted;

class ServiceProvider extends LaravelServiceProvider
{  
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    { 
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'easy-license'); 
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations'); 
        LaravelNova::serving([$this, 'servingNova']); 
        $this->registerEventListeners();
        $this->registerWebComponents(); 
        $this->registerPolicies();
        $this->configureOrderables();
        $this->configureModules();
        $this->configureMenus();

        $this->app->booted(function() {
            $this->routes();
        });
    }

    protected function routes()
    { 
        app('router')->group([
            'middleware' => 'nova',
            'namespace'  => __NAMESPACE__.'\Http\Controllers',
            'prefix'     => 'nova-api/ajax-selection'
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
            Nova\Order::class,
            Nova\Card::class,
        ]); 
    } 

    public function registerEventListeners()
    { 
        Event::listen(OrderCompleted::class, Listeners\OrderCompleted::class);
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

    public function registerWebComponents()
    {  
        \Site::push('easy-license', function($easyLicense) {
            $easyLicense->directory('easy-license');
            $easyLicense->pushComponent(new Components\Manufacturer);
            $easyLicense->pushComponent(new Components\Product);
            $easyLicense->pushComponent(new Components\Checkout);
            $easyLicense->pushComponent(new Components\Credit);
            $easyLicense->pushComponent(new Components\Order);
        });  
    }

    public function configureOrderables()
    {    
        $this->app->resolving('arminpay.order', function($manager) { 
            $manager->register(new Orderables\LicenseProduct);
        });
    }

    public function configureModules()
    {    
        \Config::set('module.locatables.manufacturer', [
            'title' => 'Manufacturer', 
            'name'  => 'manufacturer',
            'items' => [ConfigLocate::class, 'all'],
        ]);  
    }

    public function configureMenus()
    {
        
        \Config::set('menu.menuables.manufacturer', [
            'title' => 'Manufacturer',
            'callback' => [ConfigLocate::class, 'active'],
        ]);   
    } 
}
