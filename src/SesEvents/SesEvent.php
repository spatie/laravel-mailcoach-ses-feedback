<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Carbon\Carbon;
use DateTimeInterface;
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

    public function getTimestamp(): ?DateTimeInterface
    {
        $eventType = strtolower($this->payload['eventType']);

        $timestamp = $this->payload[$eventType]['timestamp'];

        return $timestamp ? Carbon::parse($timestamp)->setTimezone(config('app.timezone')) : null;
    }
}
