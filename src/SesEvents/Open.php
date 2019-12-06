<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\Send;

class Open extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Open';
    }

    public function handle(Send $send)
    {
        if (! $send->campaign->track_opens) {
            return;
        }

        $send->registerOpen();
    }
}
