<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Click extends SesEvent
{
    public function canHandlePayload(): bool
    {
        return $this->payload['eventType'] === 'Click';
    }

    public function handle(CampaignSend $campaignSend)
    {
        $campaignSend->registerClick($this->payload['click']['link']);
    }
}
