<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\Mailcoach\Models\CampaignSend;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall->payload['Message'], true);

        $messageId = $payload['mail']['messageId'];

        /** @var \Spatie\Mailcoach\Models\CampaignSend $campaignSend */
        $campaignSend = CampaignSend::findByTransportMessageId($messageId);

        if (!$campaignSend) {
            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($campaignSend);
    }
}
