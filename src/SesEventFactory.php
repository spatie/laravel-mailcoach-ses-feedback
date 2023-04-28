<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\MailcoachSesFeedback\SesEvents\Click;
use Spatie\MailcoachSesFeedback\SesEvents\Complaint;
use Spatie\MailcoachSesFeedback\SesEvents\Open;
use Spatie\MailcoachSesFeedback\SesEvents\Other;
use Spatie\MailcoachSesFeedback\SesEvents\PermanentBounce;
use Spatie\MailcoachSesFeedback\SesEvents\SesEvent;
use Spatie\MailcoachSesFeedback\SesEvents\SoftBounce;

class SesEventFactory
{
    protected static array $sesEvents = [
        Click::class,
        Complaint::class,
        Open::class,
        PermanentBounce::class,
        SoftBounce::class,
    ];

    public static function createForPayload(array $payload): SesEvent
    {
        $sesEvent = collect(static::$sesEvents)
            ->map(fn (string $sesEventClass) => new $sesEventClass($payload))
            ->first(fn (SesEvent $sesEvent) => $sesEvent->canHandlePayload());

        return $sesEvent ?? new Other($payload);
    }
}
