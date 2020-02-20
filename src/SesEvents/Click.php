<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\Send;

class Click extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Click';
    }

    public function handle(Send $send)
    {
        $send->registerClick($this->payload['click']['link']);

        $this->storeSESMessageId();
    }
}
