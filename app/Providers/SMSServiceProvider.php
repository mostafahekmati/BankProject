<?php

namespace App\Providers;

use App\Services\Sms\KavenegarSMSProvider;
use App\Services\Sms\SmsInterface;
use App\Services\Sms\SMSService;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SmsInterface::class, KavenegarSMSProvider::class);
        $this->app->singleton(SMSService::class, function ($app) {
            return new SMSService($app->make(SmsInterface::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
