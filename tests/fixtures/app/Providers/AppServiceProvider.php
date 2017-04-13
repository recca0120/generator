<?php

namespace App\Providers;

use App\Repositories\FooBarRepository;
use App\Repositories\Contracts\FooBarRepository as FooBarRepositoryContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->registerRepositories();
    }

    protected function registerRepositories()
    {
        $this->app->singleton(FooBarRepositoryContract::class, FooBarRepository::class);
    }
}
