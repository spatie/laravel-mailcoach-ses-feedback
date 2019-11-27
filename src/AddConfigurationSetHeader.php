<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSending;

class AddConfigurationSetHeader
{
    public function handle(MessageSending $event)
    {
        if (config('mail.driver') === 'ses' && $configuration_set = config('mailcoach.ses_feedback.configuration_set')) {
            $event->message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', $configuration_set);
        }
    }
}
