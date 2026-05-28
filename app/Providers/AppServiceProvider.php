<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Relation::morphMap([
            'License'  => 'App\Models\License',
            'Vendor'   => 'App\Models\Vendor',
            'Document' => 'App\Models\Document',
            'User'     => 'App\Models\User',
            'Payment'  => 'App\Models\Payment',
            'Invoice'  => 'App\Models\Invoice',
        ]);
    }
}
