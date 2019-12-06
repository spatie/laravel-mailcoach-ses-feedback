<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\Send;

abstract class SesEvent
{
    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(Send $send);
}
