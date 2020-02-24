<?php

namespace Spatie\MailcoachSesFeedback;

use Aws\Sns\Message;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;

class SesWebhookCall extends WebhookCall
{
    protected $table = 'webhook_calls';

    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {
        $message = Message::fromRawPostData();

        return self::create([
            'name' => $config->name,
            'payload' => $message->toArray(),
        ]);
    }

    public function isFirstOfThisSesMessage(): bool
    {
        $first_message_id = (int) SesWebhookCall::where('payload->MessageId', $this->payload['MessageId'])
                                                    ->min('id');

        return $this->id === $first_message_id;
    }
}
