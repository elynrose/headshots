<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class FalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('fal', function ($app) {
            return new \App\Services\FalService(
                config('fal.api_key'),
                config('fal.api_url'),
                config('fal.webhook_url')
            );
        });
    }

    public function boot()
    {
        // Register fal.ai webhook route
        if (config('fal.webhook_url')) {
            $this->app['router']->post('webhooks/fal', 'App\Http\Controllers\WebhookController@handleFalWebhook')
                ->name('webhooks.fal');
        }

        // Configure HTTP client for fal.ai
        Http::macro('fal', function () {
            return Http::withHeaders([
                'Authorization' => 'Key ' . config('fal.api_key'),
                'Content-Type' => 'application/json',
            ])->baseUrl(config('fal.api_url'));
        });
    }
} 