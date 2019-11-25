<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\Mailcoach\Models\CampaignSend;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = json_decode($this->webhookCall->payload['Message'], true);

        /** @var \Spatie\Mailcoach\Models\CampaignSend $campaignSend */
        $campaignSend = CampaignSend::findByTransportMessageId($payload['mail']['messageId']);

        if (! $campaignSend) {
            return;
        }

        if ($this->isPermanentBounce($payload)) {
            $campaignSend->markAsBounced();

            return;
        }

        if ($this->isComplaint($payload)) {
            $campaignSend->complaintReceived();

            return;
        }
    }

    protected function isPermanentBounce(array $payload): bool
    {
        if ($payload['notificationType'] !== 'Bounce') {
            return false;
        }

        if ($payload['bounce']['bounceType'] !== 'Permanent') {
            return false;
        }

        return true;
    }

    protected function isComplaint(array $payload): bool
    {
        return $payload['notificationType'] === 'Complaint';
    }
}
