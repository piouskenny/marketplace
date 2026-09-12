<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Services\PaymentProviders\PaystackGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the PaymentGateway contract to the active provider.
        // To switch providers (e.g. Flutterwave), change only this binding.
        $this->app->bind(PaymentGateway::class, PaystackGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

