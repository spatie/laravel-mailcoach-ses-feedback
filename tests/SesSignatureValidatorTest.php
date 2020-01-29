<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Exception;
use Illuminate\Http\Request;
use Spatie\MailcoachSesFeedback\SesSignatureValidator;
use Spatie\MailcoachSesFeedback\SesWebhookConfig;
use Spatie\WebhookClient\WebhookConfig;

class SesSignatureValidatorTest extends TestCase
{
    private WebhookConfig $config;

    private SesSignatureValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = SesWebhookConfig::get();

        $this->validator = new SesSignatureValidator();
    }

    private function validParams(array $overrides = []): array
    {
        return array_merge($this->getStub('bounceWebhookContent'), $overrides);
    }

    /** @test */
    public function it_requires_signature_data()
    {
        $request = new Request($this->validParams());

        $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] = 'SubscriptionConfirmation';

        $this->assertTrue($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_calls_the_subscribe_url_when_its_a_subscription_confirmation_requests()
    {
        $request = new Request($this->validParams([
            'Type' => 'SubscriptionConfirmation',
            'SubscribeURL' => url('test-route'),
            'Token' => '',
        ]));

        $this->expectException(Exception::class);

        $this->validator->isValid($request, $this->config);
    }
}
