<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;
use Spatie\MailcoachSesFeedback\Models\SesProcessedMessage;

class ProcessSesWebhooksProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        try {
            $message = Message::fromRawPostData();

            return !SesProcessedMessage::whereSesMessageId($message->MessageId)->exists();
        } catch (Exception $e) {
            return false;
        }
    }
}
