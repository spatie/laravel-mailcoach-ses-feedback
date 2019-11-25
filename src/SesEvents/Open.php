<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Open extends SesEvent
{
    public function canHandlePayload()
    {
        return false;
    }

    public function handle(CampaignSend $campaignSend)
    {
    }
}
