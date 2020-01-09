<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Models\Send;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\ProcessWebhookJob;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        $this->queue = config('mailcoach.perform_on_queue.process_feedback_job');
    }

    public function handle()
    {
        $validator = new MessageValidator();

        try {
            $message = Message::fromJsonString(json_encode($this->webhookCall->payload));
        } catch (\Exception $e) {
            $this->webhookCall->delete();
            return;
        }

        if (! $validator->isValid($message)) {
            $this->webhookCall->delete();
            return;
        }

        $payload = json_decode($this->webhookCall->payload['Message'], true);

        if (! $messageId = Arr::get($payload, 'mail.messageId')) {
            return;
        };

        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = Send::findByTransportMessageId($messageId);

        if (!$send) {
            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($send);
    }
}
