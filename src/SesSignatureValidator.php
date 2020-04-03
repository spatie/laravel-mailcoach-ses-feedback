<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Illuminate\Http\Request;
use RuntimeException;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class SesSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        try {
            $message = Message::fromJsonString((string) $request->getContent());

            if ($message['Type'] === 'SubscriptionConfirmation') {
                $this->confirmSubscription($message);
            }

            return true;
        } catch (RuntimeException $exception) {
            return false;
        }
    }

    protected function confirmSubscription(Message $message): void
    {
        file_get_contents($message['SubscribeURL']);
    }
}
