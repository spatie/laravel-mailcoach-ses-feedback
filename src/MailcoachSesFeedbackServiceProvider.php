<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailcoachSesFeedbackServiceProvider extends ServiceProvider
{
    public function register()
    {
        Route::macro('sesFeedback', function (string $url) {
            return Route::post($url, '\Spatie\MailcoachSesFeedback\SesWebhookController');
        });

        Event::listen(MessageSent::class, StoreTransportMessageId::class);
    }
}
