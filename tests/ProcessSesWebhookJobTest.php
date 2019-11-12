<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Spatie\Mailcoach\Models\CampaignSend;
use Spatie\Mailcoach\Models\CampaignSendBounce;
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
            'payload' => $this->getStub(),
        ]);

        $this->campaignSend = factory(CampaignSend::class)->create([
            'transport_message_id' => '000001378603177f-7a5433e7-8edb-42ae-af10-f0181f34d6ee-000000',
        ]);
    }

    /** @test */
    public function it_processes_a_ses_webhook_call()
    {
        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(1, CampaignSendBounce::count());
        $this->assertEquals('permanent', CampaignSendBounce::first()->severity);
        $this->assertTrue($this->campaignSend->is(CampaignSendBounce::first()->campaignSend));
    }

    /** @test */
    public function it_only_saves_when_event_is_a_failure()
    {
        $data = $this->webhookCall->payload;
        $message = json_decode($data['Message'], true);
        $message['notificationType'] = 'Delivery';
        $data['Message'] = json_encode($message);

        $this->webhookCall->update([
            'payload' => $data,
        ]);

        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(0, CampaignSendBounce::count());
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

        $this->assertEquals(0, CampaignSendBounce::count());
    }
}
