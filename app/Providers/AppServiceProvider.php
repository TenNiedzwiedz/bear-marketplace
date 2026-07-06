<?php

namespace App\Providers;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        // Store reportable morphs as stable short aliases rather than FQCNs.
        Relation::morphMap([
            'listing' => Listing::class,
            'user' => User::class,
        ]);
    }
}
