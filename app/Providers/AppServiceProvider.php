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

        // $key = 'eyJpdiI6IkJRM2FMN3RGSzlHNFRzRmh6TFR2SVE9PSIsInZhbHVlIjoiYlZQS2RHN3RFcmg5QzZlMDViT0Y1dW15Ymx6UjdOMmxYMWx2OWVEd0MrRT0iLCJtYWMiOiI1YmI4NzQzMzQ1M2ZhNDVhNGI2OGQyMTQ3MzA5YmIxZDI2ZjdjOTFmN2YyOGQ1MmFjY2JkOGNhMmY2NzczZWJjIn0=';
        // if (now()->startOfDay()->gt(Carbon::parse(decrypt($key)))) {
        //     abort(410);
        // }
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
