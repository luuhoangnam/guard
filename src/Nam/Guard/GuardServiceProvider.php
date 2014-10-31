<?php

namespace Nam\Guard;

use Illuminate\Support\ServiceProvider;

/**
 * Class GuardServiceProvider
 * @package Nam\Guard
 */
class GuardServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('nam/guard');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFilters();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ ];
    }

    protected function registerFilters()
    {
        $router = $this->app->make('router');
        $router->filter('acl', 'Nam\Guard\Filters\Acl');
        $router->filter('permissions', 'Nam\Guard\Filters\Permissions');
    }

}
