<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\CampaignSend;

abstract class SesEvent
{
    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(CampaignSend $campaignSend);
}
