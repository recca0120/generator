<?php

namespace Recca0120\Generator;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/generator.php', 'generator');

        $this->publishes([
            __DIR__.'/../config/generator.php' => config_path('generator.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/stubs' => base_path('resources/stubs'),
        ], 'stubs');

        $this->app->singleton(Generator::class, function ($app) {
            return new Generator($app['config']['generator']);
        });

        $this->app->singleton(CommandFactory::class, function ($app) {
            return new CommandFactory($app['config']['generator'], $app[Generator::class], $app);
        });

        $this->commands($this->app->make(CommandFactory::class)->create());
    }
}
