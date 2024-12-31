<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Middleware\HandleCors;



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
    public function boot()
    {
        // Apply the CORS middleware to API routes explicitly
        Route::prefix('api')
             ->middleware(['api', HandleCors::class]) // Ensure CORS middleware is applied
             ->group(base_path('routes/api.php'));
    }
}
