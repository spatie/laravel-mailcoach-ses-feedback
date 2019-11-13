<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class SesWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.mailgun_feedback');

        return new WebhookConfig([
            'name' => 'ses-feedback',
            'signing_secret' => $config['signing_secret'],
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? SesSignatureValidator::class,
            'webhook_profile' =>  $config['webhook_profile'] ?? ProcessEverythingWebhookProfile::class,
            'webhook_model' => $config['webhook_model'] ?? WebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessSesWebhookJob::class,
        ]);
    }
}
