<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('employee', \App\Repositories\EmployeeRepository::class);
        $this->app->singleton('applookup', \App\Repositories\AppLookupRepository::class);
        $this->app->singleton('advisor', \App\Repositories\AdvisorRepository::class);
        $this->app->singleton('disclaimers', \App\Repositories\DisclaimerRepository::class);
        $this->app->singleton('child', \App\Repositories\ChildRepository::class);
        $this->app->singleton('users', \App\Repositories\UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
