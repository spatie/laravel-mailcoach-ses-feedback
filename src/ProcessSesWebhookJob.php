<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\Mailcoach\Models\Send;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall->payload['Message'], true);

        $messageId = $payload['mail']['messageId'];

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = Send::findByTransportMessageId($messageId);

        if (!$send) {
            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($send);
    }
}
