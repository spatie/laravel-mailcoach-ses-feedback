<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Aws\Sns\Message;
use Spatie\Mailcoach\Models\CampaignSend;
use Spatie\Mailcoach\Models\CampaignSendFeedbackItem;
use Spatie\MailcoachSesFeedback\ProcessSesWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessSesWebhookJobTest extends TestCase
{
    /** @var \Spatie\WebhookClient\Models\WebhookCall */
    private $webhookCall;

    /** @var \Spatie\Mailcoach\Models\CampaignSend */
    private $campaignSend;

    public function setUp(): void
    {
        parent::setUp();

        $this->webhookCall = WebhookCall::create([
            'name' => 'ses',
            'payload' => $this->getStub('bounceWebhookContent'),
        ]);

        $this->campaignSend = factory(CampaignSend::class)->create([
            'transport_message_id' => '0107016eb1654604-5f27d09d-872f-4a34-be34-c4e24741cb66-000000',
        ]);
    }

    /** @test */
    public function it_processes_a_ses_webhook_call()
    {
        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(1, CampaignSendFeedbackItem::count());
        $this->assertTrue($this->campaignSend->is(CampaignSendFeedbackItem::first()->campaignSend));
    }

    /** @test */
    public function it_does_nothing_when_it_cannot_find_the_transport_message_id()
    {
        $data = $this->webhookCall->payload;
        $message = json_decode($data['Message'], true);
        $message['mail']['messageId'] = 'some-other-id';
        $data['Message'] = json_encode($message);

        $this->webhookCall->update([
            'payload' => $data,
        ]);

        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(0, CampaignSendFeedbackItem::count());
    }
}
