<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailcoachSesFeedbackServiceProvider extends ServiceProvider
{
    public function register()
    {
        Route::macro('sesFeedback', fn (string $url) => Route::post("{$url}/{mailer?}", '\\' . SesWebhookController::class));

        Event::listen(MessageSending::class, AddConfigurationSetHeader::class);
        Event::listen(MessageSent::class, StoreTransportMessageId::class);
    }
}
