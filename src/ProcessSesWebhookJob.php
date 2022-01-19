<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessSesWebhookJob extends ProcessWebhookJob
{
    use UsesMailcoachModels;

    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        $this->queue = config('mailcoach.campaigns.perform_on_queue.process_feedback_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if (! $this->validateMessageFromWebhookCall()) {
            $this->webhookCall->delete();

            return;
        }

        if (! $this->webhookCall->isFirstOfThisSesMessage()) {
            $this->webhookCall->delete();

            return;
        }

        $payload = json_decode($this->webhookCall->payload['Message'], true);

        if (! $messageId = Arr::get($payload, 'mail.messageId')) {
            $this->markAsProcessed();

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $sendModelClass = $this->getSendClass();

        $send = $sendModelClass::findByTransportMessageId($messageId);

        if (! $send) {
            $this->markAsProcessed();

            return;
        }

        if (! Arr::get($payload, 'eventType')) {
            $this->markAsProcessed();

            return;
        }

        $sesEvent = SesEventFactory::createForPayload($payload);

        $sesEvent->handle($send);

        $this->markAsProcessed();
    }

    protected function markAsProcessed(): void
    {
        event(new WebhookCallProcessedEvent($this->webhookCall));
    }

    protected function validateMessageFromWebhookCall(): bool
    {
        $validator = resolve(MessageValidator::class);

        try {
            $message = Message::fromJsonString(json_encode($this->webhookCall->payload));
        } catch (Exception) {
            return false;
        }

        return $validator->isValid($message);
    }
}
