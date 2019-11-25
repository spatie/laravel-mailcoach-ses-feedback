<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Click extends SesEvent
{
    public function canHandlePayload()
    {
        return;
    }

    public function handle(CampaignSend $campaignSend)
    {
    }
}
