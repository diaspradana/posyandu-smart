<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Tapos;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Policies\TaposPolicy;
use App\Policies\BalitaPolicy;
use App\Policies\IbuHamilPolicy;

use Illuminate\Pagination\Paginator;

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
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom-simple');

        Gate::policy(Tapos::class, TaposPolicy::class);
        Gate::policy(Balita::class, BalitaPolicy::class);
        Gate::policy(IbuHamil::class, IbuHamilPolicy::class);
    }
}

