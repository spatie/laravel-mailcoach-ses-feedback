<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\Send;

class Complaint extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Complaint';
    }

    public function handle(Send $send)
    {
        $send->registerComplaint();
    }
}
