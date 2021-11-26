<?php

namespace App\Providers;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message, $code) {
            return response([
                'message' => $message,
                'status' => $code,
            ], $code);
        });

        Response::macro('error', function ($message, $code) {
            return response([
                'message' => $message,
                'status' => $code,
            ], $code);
        });
    }
}
