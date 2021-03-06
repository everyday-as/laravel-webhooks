<?php

namespace GmodStore\LaravelWebhooks;

use GmodStore\LaravelWebhooks\Console\WebhookMakeCommand;
use GuzzleHttp\Client;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-webhooks.php' => config_path('laravel-webhooks.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebhookMakeCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-webhooks.php',
            'laravel-webhooks'
        );

        $this->app->singleton('laravel-webhooks:client', function ($app) {
            return new Client($app['config']['laravel-webhooks']['guzzle']);
        });
    }
}
