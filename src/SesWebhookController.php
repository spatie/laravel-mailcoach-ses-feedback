<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProcessor;

class SesWebhookController
{
    public function __invoke(Request $request)
    {
        $webhookConfig = SesWebhookConfig::get();

        logger(json_encode(json_decode(file_get_contents('php://input'), true)));
        logger(json_decode(file_get_contents('php://input'), true));

        (new WebhookProcessor($request, $webhookConfig))->process();

        return response()->json(['message' => 'ok']);
    }
}
