<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Aws\Sns\Message;
use Spatie\Mailcoach\Models\CampaignClick;
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
    public function it_processes_a_ses_webhook_call_for_a_bounce()
    {
        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(1, CampaignSendFeedbackItem::count());
        tap(CampaignSendFeedbackItem::first(), function (CampaignSendFeedbackItem $campaignSendFeedbackItem) {
            $this->assertTrue($this->campaignSend->is($campaignSendFeedbackItem->campaignSend));
            $this->assertEquals('bounce', $campaignSendFeedbackItem->type);
        });
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_clicks()
    {
        $webhookCall = WebhookCall::create([
            'name' => 'ses',
            'payload' => $this->getStub('clickWebhookContent'),
        ]);

        /** @var CampaignSend $campaignSend */
        $campaignSend = factory(CampaignSend::class)->create([
            'transport_message_id' => '0107016eb14a6683-21d61476-4ac8-4eb2-aa71-79209c70e8a4-000000',
        ]);
        $campaignSend->campaign->update(['track_clicks' => true]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, $campaignSend->clicks->count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_opens()
    {
        $webhookCall = WebhookCall::create([
            'name' => 'ses',
            'payload' => $this->getStub('openWebhookContent'),
        ]);

        /** @var CampaignSend $campaignSend */
        $campaignSend = factory(CampaignSend::class)->create([
            'transport_message_id' => '0107016eb143be75-4e95d17b-1251-4abe-b75f-f0eccf0c11ac-000000',
        ]);
        $campaignSend->campaign->update(['track_opens' => true]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, $campaignSend->opens->count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_complaints()
    {
        $webhookCall = WebhookCall::create([
            'name' => 'ses',
            'payload' => $this->getStub('complaintWebhookContent'),
        ]);

        /** @var CampaignSend $campaignSend */
        $campaignSend = factory(CampaignSend::class)->create([
            'transport_message_id' => '0107016eb149cd22-7b2d056e-8298-4cb2-b716-d7d85935a752-000000',
        ]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, CampaignSendFeedbackItem::count());
        tap(CampaignSendFeedbackItem::first(), function (CampaignSendFeedbackItem $campaignSendFeedbackItem) use ($campaignSend) {
            $this->assertTrue($campaignSend->is($campaignSendFeedbackItem->campaignSend));
            $this->assertEquals('complaint', $campaignSendFeedbackItem->type);
        });
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
