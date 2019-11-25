<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ProcessSesWebhooksProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        $payload = json_decode($request->input());

        return count($payload);
    }
}
