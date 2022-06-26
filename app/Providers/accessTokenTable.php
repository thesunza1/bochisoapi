<?php

namespace App\Providers;

use App\Models\PersonalAccessTokens;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class accessTokenTable extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //user
        Sanctum::usePersonalAccessTokenModel(PersonalAccessTokens::class);
    }
}
