<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ProcessSesWebhooksProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        try {
            Message::fromJsonString((string) $request->getContent());

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
