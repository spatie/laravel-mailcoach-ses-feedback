<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Illuminate\Mail\Events\MessageSending;
use Swift_Message;

class AddConfigurationSetHeaderTest extends TestCase
{
    /** @test **/
    public function it_adds_a_configuration_set_header_if_the_message_is_sent_by_mailcoach()
    {
        $message = new Swift_Message('Test', 'body');
        $message->getHeaders()->addTextHeader('X-MAILCOACH', true);

        config()->set('mailcoach.ses_feedback.configuration_set', 'hello');
        config()->set('mail.default', 'ses');
        config()->set('mail.mailers.ses.transport', 'ses');

        event(new MessageSending($message));

        $this->assertEquals('hello', $message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getFieldBody());
    }
}
