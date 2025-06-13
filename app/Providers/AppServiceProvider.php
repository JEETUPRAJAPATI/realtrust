<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClient;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Analytics::class, function ($app) {
            $propertyId = config('analytics.property_id');
            $client = $app->make(AnalyticsClient::class);
    
            if (!$propertyId) {
                throw new \Exception('Analytics property_id is not set.');
            }
    
            return new Analytics($client, $propertyId);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        //
    }
}
