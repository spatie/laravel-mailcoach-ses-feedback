<?php

namespace Spatie\SesFeedback;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class SesSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $validator = new MessageValidator();

        $message = count($request->all())
            ? new Message($request->all())
            : Message::fromRawPostData();

        if ($message['Type'] === 'SubscriptionConfirmation') {
            $this->confirmSubscription($message);
        }

        return $validator->isValid($message);
    }

    protected function confirmSubscription(Message $message): void
    {
        file_get_contents($message['SubscribeURL']);
    }
}
