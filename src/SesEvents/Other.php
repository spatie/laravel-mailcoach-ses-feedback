<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

class Other extends SesEvent
{
    public function canHandlePayload()
    {
        return true;
    }

    public function handle(CampaignSend $campaignSend)
    {
    }
}
