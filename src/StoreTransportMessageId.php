<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSent;

class StoreTransportMessageId
{
    public function handle(MessageSent $event)
    {
        if (! isset($event->data['send'])) {
            return;
        }

        if (! $event->message->getHeaders()->has('X-Ses-Message-ID')) {
            return;
        }

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = $event->data['send'];

        $transportMessageId = $event->message->getHeaders()->get('X-Ses-Message-ID')->getFieldBody();

        $send->storeTransportMessageId($transportMessageId);
    }
}
