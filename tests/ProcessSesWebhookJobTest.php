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
            'transport_message_id' => '93ef47baa0e7818557569e92494f4be1@swift.generated',
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
            'transport_message_id' => '441daaa28872991703a3b02a72408c62@swift.generated',
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
            'transport_message_id' => '0107018023eb0291-0bc7253b-53c2-473f-8efd-88e3637c18ce-000000',
        ]);
        $send->campaign->update([
            'track_opens' => true,
        ]);

        (new ProcessSesWebhookJob($webhookCall))->handle();

        $this->assertEquals(1, $send->opens->count());
    }

    /** @test */
    public function it_processes_a_ses_webhook_call_for_opens_with_message_id_from_header()
    {
        $webhookCall = SesWebhookCall::create([
            'name' => 'ses',
            'external_id' => $this->getStub('openWebhookContent')['MessageId'],
            'payload' => $this->getStub('openWebhookContent'),
        ]);

        /** @var Send $send */
        $send = Send::factory()->create([
            'transport_message_id' => 'ebe712eb83fab12b595b69657d2bfe55@spatie.be',
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
            'transport_message_id' => '5d5929d61c2bfd8de65f2cf07a1457de@swift.generated',
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
            'transport_message_id' => 'e56a471288e8874bb27a92b7634ef86f@swift.generated',
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
        $this->send->update(['transport_message_id' => 'some-other-id']);
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
