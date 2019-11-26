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
        if (! $campaignSend->campaign->track_opens) {
            return;
        }

        $campaignSend->registerOpen();
    }
}
