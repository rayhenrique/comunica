<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\EventRequest;
use App\Policies\EventRequestPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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
        Gate::policy(EventRequest::class, EventRequestPolicy::class);
        if ($this->app->environment('testing')) {
            // Testes não devem se conectar ao HMR de um servidor local em uso.
            Vite::useHotFile(storage_path('framework/testing.hot'));
        }
    }
}
