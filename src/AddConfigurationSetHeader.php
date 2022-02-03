<?php

namespace Spatie\MailcoachSesFeedback;

use Illuminate\Mail\Events\MessageSending;

class AddConfigurationSetHeader
{
    public function handle(MessageSending $event)
    {
        if (! $configuration_set = config('mailcoach.ses_feedback.configuration_set')) {
            return;
        }

        if (! $event->message->getHeaders()->get('X-MAILCOACH')) {
            return;
        }

        $event->message->getHeaders()->removeAll('X-SES-CONFIGURATION-SET');
        $event->message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', $configuration_set);
    }
}
