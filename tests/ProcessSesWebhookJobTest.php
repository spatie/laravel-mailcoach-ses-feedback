<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Aws\Sns\MessageValidator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\MailcoachSesFeedback\ProcessSesWebhookJob;
use Spatie\MailcoachSesFeedback\SesWebhookCall;

class ProcessSesWebhookJobTest extends TestCase
{
    use RefreshDatabase;

    private SesWebhookCall $webhookCall;

    private Send $send;

    public function setUp(): void
    {
        parent::setUp();

        $this->webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('bounceWebhookContent')['MessageId'],
            'payload' => $this->getStub('bounceWebhookContent'),
        ]);

        $this->send = Send::factory()->create([
            'transport_message_id' => '0107016eb1654604-5f27d09d-872f-4a34-be34-c4e24741cb66-000000',
        ]);

        $this->mock(MessageValidator::class)->shouldReceive('isValid')->andReturnTrue();
    }

    /** @test * */
    public function it_does_nothing_and_deletes_the_call_if_signature_is_missing()
    {
        $data = $this->getStub('bounceWebhookContent');
        $data['Signature'] = null;

        $this->webhookCall->update([
            'payload' => json_encode($data),
        ]);

        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();
        $this->assertEquals(0, SendFeedbackItem::count());
        $this->assertEquals(0, SesWebhookCall::count());
    }

    /** @test * */
    public function it_does_nothing_if_data_is_missing()
    {
        $data = $this->getStub('bounceWebhookContent');
        $data['Message'] = '';

        $this->webhookCall->update([
            'payload' => json_encode($data),
        ]);

        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();
        $this->assertEquals(0, SendFeedbackItem::count());
        $this->assertEquals(0, SesWebhookCall::count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_a_bounce()
    {
        $data = $this->getStub('bounceWebhookContent');

        $this->webhookCall->update([
            'payload' => $data,
        ]);

        $job = new ProcessSesWebhookJob($this->webhookCall);

        $job->handle();

        $this->assertEquals(1, SendFeedbackItem::count());
        tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
            $this->assertTrue($this->send->is($sendFeedbackItem->send));
            $this->assertEquals('bounce', $sendFeedbackItem->type);
            $this->assertEquals(Carbon::parse('2019-11-28T09:43:55'), $sendFeedbackItem->created_at);
        });
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_clicks()
    {
        $webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('clickWebhookContent')['MessageId'],
            'payload' => $this->getStub('clickWebhookContent'),
        ]);

        /** @var Send $send */
        $send = Send::factory()->create([
            'transport_message_id' => '0107016eb14a6683-21d61476-4ac8-4eb2-aa71-79209c70e8a4-000000',
        ]);
        $send->campaign->update(['track_clicks' => true]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, $send->clicks->count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_opens()
    {
        $webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('openWebhookContent')['MessageId'],
            'payload' => $this->getStub('openWebhookContent'),
        ]);

        /** @var Send $send */
        $send = Send::factory()->create([
            'transport_message_id' => '0107016eb143be75-4e95d17b-1251-4abe-b75f-f0eccf0c11ac-000000',
        ]);
        $send->campaign->update([
            'track_opens' => true,
        ]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, $send->opens->count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_complaints()
    {
        $webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('complaintWebhookContent')['MessageId'],
            'payload' => $this->getStub('complaintWebhookContent'),
        ]);

        /** @var Send $send */
        $send = Send::factory()->create([
            'transport_message_id' => '0107016eb149cd22-7b2d056e-8298-4cb2-b716-d7d85935a752-000000',
        ]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, SendFeedbackItem::count());
        tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) use ($send) {
            $this->assertTrue($send->is($sendFeedbackItem->send));
            $this->assertEquals('complaint', $sendFeedbackItem->type);
            $this->assertEquals(Carbon::parse('2019-11-28T09:13:57'), $sendFeedbackItem->created_at);
        });
    }

    /** @test */
    public function it_fires_an_event_when_the_webhook_is_processed()
    {
        $webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('clickWebhookContent')['MessageId'],
            'payload' => $this->getStub('clickWebhookContent'),
        ]);

        /** @var Send $send */
        $send = Send::factory()->create([
            'transport_message_id' => '0107016eb14a6683-21d61476-4ac8-4eb2-aa71-79209c70e8a4-000000',
        ]);

        Event::fake();

        (new ProcessSesWebhookJob($webhookCall))->handle();

        Event::assertDispatched(WebhookCallProcessedEvent::class);
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

        $this->assertEquals(0, SendFeedbackItem::count());
    }

    /** @test * */
    public function it_does_nothing_and_deletes_the_call_if_it_is_a_duplicate_ses_message_id()
    {
        $webhookCallSecond = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('bounceWebhookContent')['MessageId'],
            'payload' => $this->getStub('bounceWebhookContent'),
        ]);

        (new ProcessSesWebhookJob($this->webhookCall))->handle();
        (new ProcessSesWebhookJob($webhookCallSecond))->handle();

        $this->assertEquals(1, SendFeedbackItem::count());
        $this->assertEquals(1, SesWebhookCall::count());
    }
}
