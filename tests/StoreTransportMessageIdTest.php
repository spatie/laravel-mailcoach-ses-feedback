<?php

namespace Spatie\SesFeedback\Tests;

use Illuminate\Mail\Events\MessageSent;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Swift_Message;

class StoreTransportMessageIdTest extends TestCase
{
    /** @test * */
    public function it_stores_the_message_id_from_the_transport()
    {
        $pendingSend = factory(CampaignSend::class)->create();
        $message = new Swift_Message('Test', 'body');
        $message->getHeaders()->addTextHeader('X-Ses-Message-ID', '1234');

        event(new MessageSent($message, [
            'campaignSend' => $pendingSend,
        ]));

        tap($pendingSend->fresh(), function (CampaignSend $campaignSend) {
            $this->assertEquals('1234', $campaignSend->transport_message_id);
        });
    }
}
