<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class SesSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $message = count($request->all())
            ? new Message($request->all())
            : Message::fromRawPostData();

        if ($message['Type'] === 'SubscriptionConfirmation') {
            $this->confirmSubscription($message);
        }

        return true;
    }

    protected function confirmSubscription(Message $message): void
    {
        file_get_contents($message['SubscribeURL']);
    }
}
