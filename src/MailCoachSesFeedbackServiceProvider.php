<?php

namespace Spatie\MailCoachSesFeedback;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailCoachSesFeedbackServiceProvider extends ServiceProvider
{
    public function register()
    {
        Route::macro('SesFeedback', function (string $url) {
            return Route::post($url, '\Spatie\MailCoachSesFeedback\SesWebhookController');
        });

        Event::listen(MessageSent::class, StoreTransportMessageId::class);
    }
}
