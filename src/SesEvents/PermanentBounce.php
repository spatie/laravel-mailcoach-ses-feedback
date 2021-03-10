<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PermanentBounce extends SesEvent
{
    public function canHandlePayload(): bool
    {
        if ($this->payload['eventType'] !== 'Bounce') {
            return false;
        }

        if ($this->payload['bounce']['bounceType'] !== 'Permanent') {
            return false;
        }

        return true;
    }

    public function handle(Send $send)
    {
        $send->registerBounce($this->getTimestamp());
    }
}
