<?php

namespace EricPridham\Flow;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FlowServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ericpridham');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flow');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutes();

        $flow = new Flow();
        $flow->register(config('flow.watchers'));

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/flow.php', 'flow');

        // Register the service the package provides.
        $this->app->singleton('flow', function ($app) {
            return new Flow;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['flow'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/flow.php' => config_path('flow.php'),
        ], 'flow.config');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/flow'),
        ], 'flow.views');

        // Publishing assets.
        $this->publishes([
            __DIR__.'/../resources/dist' => public_path('vendor/flow'),
        ], 'flow.views');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ericpridham'),
        ], 'flow.views');*/

        // Registering package commands.
        // $this->commands([]);
    }

    public function loadRoutes()
    {
        Route::group([
            'namespace' => 'EricPridham\Flow\Http\Controllers',
            'prefix' => config('flow.path')
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        });
    }
}
