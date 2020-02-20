<?php

namespace Spatie\MailcoachSesFeedback\SesEvents;

use Spatie\Mailcoach\Models\Send;
use Spatie\MailcoachSesFeedback\Models\SesProcessedMessage;

abstract class SesEvent
{
    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    abstract public function canHandlePayload(): bool;

    abstract public function handle(Send $send);

    public function storeSESMessageId() {

        SesProcessedMessage::create([
            'ses_message_id' => $this->payload['SesMessageId']
        ]);

    }
}
