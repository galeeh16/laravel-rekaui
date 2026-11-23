<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        /*
         * The front end is rendered client-side only. Without this, the adapter would
         * try to reach the Vite SSR endpoint on every request while `npm run dev` runs.
         */
        Inertia::disableSsr();
    }
}
