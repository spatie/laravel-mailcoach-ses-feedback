<?php

namespace Spatie\MailcoachSesFeedback;

use Spatie\WebhookClient\WebhookConfig;

class SesWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.ses_feedback');

        return new WebhookConfig([
            'name' => 'ses-feedback',
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? SesSignatureValidator::class,
            'webhook_profile' => $config['webhook_profile'] ?? ProcessSesWebhooksProfile::class,
            'webhook_model' => $config['webhook_model'] ?? SesWebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessSesWebhookJob::class,
        ]);
    }
}
