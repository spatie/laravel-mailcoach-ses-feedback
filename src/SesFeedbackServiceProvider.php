<?php

namespace Spatie\SesFeedback;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SesFeedbackServiceProvider extends ServiceProvider
{
    public function register()
    {
        Route::macro('sesFeedback', function (string $url) {
            return Route::post($url, '\Spatie\SesFeedback\SesWebhookController');
        });

        Event::listen(MessageSent::class, StoreTransportMessageId::class);
    }
}
