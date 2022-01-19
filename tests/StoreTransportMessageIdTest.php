<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Illuminate\Mail\Events\MessageSent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\MailcoachSesFeedback\Tests\factories\SendFactory;
use Swift_Message;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

class StoreTransportMessageIdTest extends TestCase
{
    /** @test **/
    public function it_stores_the_message_id_from_the_transport()
    {
        $pendingSend = (new SendFactory())->create();
        $message = (new Email())->setBody(new TextPart('body'));
        $message->getHeaders()->addTextHeader('X-Ses-Message-ID', '1234');

        event(new MessageSent($message, [
            'send' => $pendingSend,
        ]));

        tap($pendingSend->fresh(), function (Send $send) {
            $this->assertEquals('1234', $send->transport_message_id);
        });
    }
}
