<?php

namespace Recca0120\Generator;

use Illuminate\Support\ServiceProvider;
use Recca0120\Generator\Console\ViewMakeCommand;
use Recca0120\Generator\Console\ModelMakeCommand;
use Recca0120\Generator\Console\RequestMakeCommand;
use Recca0120\Generator\Console\ScaffoldMakeCommand;
use Recca0120\Generator\Console\PresenterMakeCommand;
use Recca0120\Generator\Console\ControllerMakeCommand;
use Recca0120\Generator\Console\RepositoryMakeCommand;
use Recca0120\Generator\Console\RepositoryContractMakeCommand;

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
        if ($this->app->runningInConsole() === true) {
            $this->commands([
                ViewMakeCommand::class,
                ModelMakeCommand::class,
                RequestMakeCommand::class,
                ScaffoldMakeCommand::class,
                PresenterMakeCommand::class,
                ControllerMakeCommand::class,
                RepositoryMakeCommand::class,
                RepositoryContractMakeCommand::class,
            ]);
        }
    }
}
