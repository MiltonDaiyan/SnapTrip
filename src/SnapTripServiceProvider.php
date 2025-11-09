<?php

namespace DaiyanMozumder\SnapTrip;

use Illuminate\Support\ServiceProvider;
use DaiyanMozumder\SnapTrip\Services\SnapTripService;

class SnapTripServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/snaptrip.php', 'snaptrip');

        $this->app->singleton('snaptrip', function () {
            return new SnapTripService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/snaptrip.php' => config_path('snaptrip.php'),
        ], 'config');
    }
}
