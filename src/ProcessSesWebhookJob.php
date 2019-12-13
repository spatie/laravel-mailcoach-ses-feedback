<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Models\Send;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall->payload['Message'], true);

        if (! $messageId = Arr::get($payload, 'mail.messageId')) {
            return;
        };

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = Send::findByTransportMessageId($messageId);

        if (!$send) {
            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($send);
    }
}
