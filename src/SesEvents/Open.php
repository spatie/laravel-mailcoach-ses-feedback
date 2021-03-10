<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class Open extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Open';
    }

    public function handle(Send $send)
    {
        $send->registerOpen($this->getTimestamp());
    }
}
