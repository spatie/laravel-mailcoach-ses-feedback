<?php

namespace Spatie\MailcoachSesFeedback\Tests\SesEvents;

use Generator;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\SoftBounceRegisteredEvent;
use Spatie\MailcoachSesFeedback\SesEvents\SoftBounce;
use Spatie\MailcoachSesFeedback\Tests\TestCase;

class SoftBounceTest extends TestCase
{
    /** @test */
    public function it_can_handle_a_soft_bounce_event()
    {
        Event::fake();

        $event = new SoftBounce([
            'eventType' => 'Bounce',
            'bounce' => [
                'bounceType' => 'Undetermined',
                'timestamp' => 1610000000,
            ],
        ]);

        $this->assertTrue($event->canHandlePayload());

        $event->handle(SendFactory::new()->create());

        Event::assertDispatched(SoftBounceRegisteredEvent::class);
    }

    /**
     * @test
     * @dataProvider failures
     */
    public function it_cannot_handle_soft_bounces(array $payload)
    {
        $event = new SoftBounce($payload);

        $this->assertFalse($event->canHandlePayload());
    }

    public function failures(): Generator
    {
        yield 'different event' => [
            [
                'eventType' => 'SomethingElse',
                'bounce' => ['bounceType' => 'Undetermined'],
            ],
        ];

        yield 'different type' => [
            [
                'eventType' => 'Bounce',
                'bounce' => ['bounceType' => 'Permanent'],
            ],
        ];
    }
}
