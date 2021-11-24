<?php

namespace App\Providers;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
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
        Response::macro('success', function ($message, $code) {
            return response([
                //'status' => (bool) $data,
                'message' => $message,
                'status' => $code,
            ], $code);
        });

        Response::macro('error', function ($message, $code) {
            return response([
                //'status' => (bool) $data,
                'message' => $message,
                'status' => $code,
            ], $code);
        });
    }
}
