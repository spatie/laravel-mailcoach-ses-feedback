<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Complaint extends SesEvent
{
    public function canHandlePayload()
    {
        return $this->payload['notificationType'] === 'Complaint';
    }

    public function handle(CampaignSend $campaignSend)
    {
        $campaignSend->complaintReceived();
    }
}
