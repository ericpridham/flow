<?php

namespace EricPridham\Flow;

use EricPridham\Flow\Console\InstallCommand;
use EricPridham\Flow\Console\PurgeCommand;
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'flow');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (config('flow.enabled')) {
            $flow = new Flow();
            $flow->registerWatchers(config('flow.watchers')??[]);
            $flow->registerRecorders(config('flow.recorders')??[]);
        }

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

        $this->app->singleton('flow', function ($app) {
            return new Flow;
        });

        $this->commands([
            InstallCommand::class,
            PurgeCommand::class,
        ]);
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
    }
}
