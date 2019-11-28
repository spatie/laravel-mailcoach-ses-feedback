<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSending;

class AddConfigurationSetHeader
{
    public function handle(MessageSending $event)
    {
        if (! config('mail.driver') === 'ses') {
            return;
        }

        if (! $configuration_set = config('mailcoach.ses_feedback.configuration_set')) {
            return;
        }

        $event->message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', $configuration_set);
    }
}
