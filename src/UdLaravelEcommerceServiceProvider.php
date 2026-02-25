<?php

namespace Unusualdope\LaravelEcommerce;

use Illuminate\Support\ServiceProvider;
use Unusualdope\LaravelEcommerce\Providers\SocialiteConfigServiceProvider;

class UdLaravelEcommerceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->mergeTypesenseScoutConfig();

        /*
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ecommerce');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ecommerce');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ud-ecommerce.php' => config_path('ud-ecommerce.php'),
                __DIR__ . '/../config/currencies.php' => config_path('currencies.php'),
            ], 'config');


            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/ud-laravel-ecommerce'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/ud-laravel-ecommerce'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/ud-laravel-ecommerce'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/ud-ecommerce.php', 'ud-ecommerce');
        $this->mergeConfigFrom(__DIR__ . '/../config/currencies.php', 'currencies');
        // Register the main class to use with the facade
        $this->app->singleton('ud-ecommerce', function () {
            return new UdLaravelEcommerce;
        });

        // Register Socialite dynamic configuration provider
        $this->app->register(SocialiteConfigServiceProvider::class);
    }

    /**
     * Merge package Typesense config into Laravel Scout so SCOUT_DRIVER=typesense works.
     */
    protected function mergeTypesenseScoutConfig(): void
    {
        $packageTypesense = require __DIR__.'/../config/scout_typesense.php';
        $existing = config('scout.typesense', []);
        config(['scout.typesense' => array_replace_recursive($packageTypesense, $existing)]);
    }
}
