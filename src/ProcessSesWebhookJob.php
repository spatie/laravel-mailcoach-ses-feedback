<?php

namespace Spatie\MailCoachSesFeedback;

use Spatie\MailCoach\Models\CampaignSend;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall->payload['Message'], true);

        /** @var \Spatie\MailCoach\Models\CampaignSend $campaignSend */
        $campaignSend = CampaignSend::findByTransportMessageId($payload['mail']['messageId']);

        if (! $campaignSend) {
            return;
        }

        if ($payload['notificationType'] !== 'Bounce') {
            return;
        }

        $campaignSend->markAsBounced(strtolower($payload['bounce']['bounceType']));
    }
}
