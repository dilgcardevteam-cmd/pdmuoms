<?php

namespace App\Providers;

use App\Models\LocallyFundedProject;
use App\Observers\LocallyFundedProjectObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LocallyFundedProject::observe(LocallyFundedProjectObserver::class);
    }
}
