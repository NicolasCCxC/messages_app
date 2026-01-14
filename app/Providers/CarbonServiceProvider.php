<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class CarbonServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Carbon::macro('diffInYearsInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInYears($date, $absolute, $utc));
        });

        Carbon::macro('diffInMonthsInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInMonths($date, $absolute, $utc));
        });

        Carbon::macro('diffInDaysInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInDays($date, $absolute, $utc));
        });

        Carbon::macro('diffInHoursInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInHours($date, $absolute, $utc));
        });

        Carbon::macro('diffInMinutesInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInMinutes($date, $absolute, $utc));
        });

        Carbon::macro('diffInSecondsInt', function ($date = null, $absolute = true, $utc = false) {
            return intval($this->diffInSeconds($date, $absolute, $utc));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}