<?php

namespace GmodStore\LaravelWebhooks;

use GuzzleHttp\Client;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-webhooks.php' => config_path('laravel-webhooks.php'),
        ], 'config');

        $this->app->singleton('laravel-webhooks:client', function ($app) {
            return new Client([
                'timeout' => max($app['config']['laravel-webhooks']['http']['timeout'], 0),
            ]);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-webhooks.php', 'laravel-webhooks'
        );
    }
}
