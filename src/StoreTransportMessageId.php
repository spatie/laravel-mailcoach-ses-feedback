<?php

namespace Spatie\SesFeedback;

use Illuminate\Mail\Events\MessageSent;

class StoreTransportMessageId
{
    public function handle(MessageSent $event)
    {
        if (! isset($event->data['campaignSend'])) {
            return;
        }

        if (! $event->message->getHeaders()->has('X-Ses-Message-ID')) {
            return;
        }

        /** @var \Spatie\EmailCampaigns\Models\CampaignSend $campaignSend */
        $campaignSend = $event->data['campaignSend'];

        $transportMessageId = $event->message->getHeaders()->get('X-Ses-Message-ID')->getFieldBody();

        $campaignSend->storeTransportMessageId($transportMessageId);
    }
}
