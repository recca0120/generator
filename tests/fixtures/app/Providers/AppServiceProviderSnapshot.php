<?php

namespace App\Providers;

use App\Repositories\FooBarRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\FooBarRepository as FooBarRepositoryContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
        //
    }

    protected function registerRepositories()
    {
        $this->app->singleton(FooBarRepositoryContract::class, FooBarRepository::class);
    }
}
