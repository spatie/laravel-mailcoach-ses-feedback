<?php

namespace Spatie\MailCoachSesFeedback;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProcessor;

class SesWebhookController
{
    public function __invoke(Request $request)
    {
        $webhookConfig = SesWebhookConfig::get();

        (new WebhookProcessor($request, $webhookConfig))->process();

        return response()->json(['message' => 'ok']);
    }
}
