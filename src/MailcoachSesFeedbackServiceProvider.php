<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailcoachSesFeedbackServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->bootPublishables();
    }

    public function register()
    {
        Route::macro('sesFeedback', fn (string $url) => Route::post($url, '\\' . SesWebhookController::class));

        Event::listen(MessageSending::class, AddConfigurationSetHeader::class);
        Event::listen(MessageSent::class, StoreTransportMessageId::class);
    }

    protected function bootPublishables()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_mailcoach_ses_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailcoach_ses_tables.php'),
        ], 'mailcoach-ses-feedback-migrations');

        return $this;
    }
}
