<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Complaint extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['notificationType'] === 'Complaint';
    }

    public function handle(CampaignSend $campaignSend)
    {
        $campaignSend->complaintReceived();
    }
}
