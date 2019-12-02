<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Illuminate\Mail\Events\MessageSending;
use Swift_Message;

class AddConfigurationSetHeaderTest extends TestCase
{
    /** @test **/
    public function it_adds_a_configuration_set_header()
    {
        $message = new Swift_Message('Test', 'body');

        config()->set('mailcoach.ses_feedback.configuration_set', 'hello');
        config()->set('mail.driver', 'ses');

        event(new MessageSending($message));

        $this->assertEquals('hello', $message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getFieldBody());
    }
}
