<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Open extends SesEvent
{
    public function canHandlePayload()
    {
        return $this->payload['notificationType'] === 'Open';
    }

    public function handle(CampaignSend $campaignSend)
    {
        $campaignSend->registerOpen();
    }
}
