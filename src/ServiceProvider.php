<?php

namespace SilverCO\RestHooks;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        //
    }

    /**
     * Bootstrap package services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'resthooks');

        $this->setUpConfiguration();
        $this->registerRoutes();
    }

    private function setUpConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../config/resthooks.php' => config_path('resthooks.php'),
            __DIR__ . '/../lang' => $this->app->langPath('vendor/resthooks'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/resthooks.php',
            'resthooks'
        );
    }

    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Retrieve routes configuration.
     *
     * @return array
     */
    private function routeConfiguration(): array
    {
        return [
            'prefix' => Config::get('resthooks.routes.prefix'),
            'middleware' => Config::get('resthooks.routes.middlewares'),
        ];
    }
}
