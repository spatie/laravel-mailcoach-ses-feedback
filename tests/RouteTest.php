<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Illuminate\Support\Facades\Route;

class RouteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::sesFeedback('ses-feedback');
    }

    /** @test */
    public function it_provides_a_route_macro_to_handle_webhooks()
    {
        $validPayload = $this->getStub('bounceWebhookContent');

        $response = $this->postJson('ses-feedback', $validPayload);

        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
