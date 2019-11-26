<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Click extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['notificationType'] === 'Click';
    }

    public function handle(CampaignSend $campaignSend)
    {
        if (! $campaignSend->campaign->track_clicks) {
            return;
        }

        $campaignSend->registerClick($this->payload['click']['link']);
    }
}
