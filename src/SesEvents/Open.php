<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Open extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['notificationType'] === 'Open';
    }

    public function handle(CampaignSend $campaignSend)
    {
        $campaignSend->registerOpen();
    }
}
