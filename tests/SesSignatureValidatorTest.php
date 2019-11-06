<?php

namespace Spatie\SesFeedback\Tests;

use Illuminate\Http\Request;
use Spatie\SesFeedback\SesSignatureValidator;
use Spatie\SesFeedback\SesWebhookConfig;

class SesSignatureValidatorTest extends TestCase
{
    /** @var \Spatie\WebhookClient\WebhookConfig */
    private $config;

    /** @var \Spatie\SesFeedback\SesSignatureValidator */
    private $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = SesWebhookConfig::get();

        $this->validator = new SesSignatureValidator();
    }

    private function validParams(array $overrides = []): array
    {
        return array_merge($this->getStub(), $overrides);
    }

    /** @test */
    public function it_requires_signature_data()
    {
        $request = new Request($this->validParams());

        $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] = 'SubscriptionConfirmation';

        $this->assertTrue($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_fails_if_signature_is_missing()
    {
        $request = new Request($this->validParams([
            'Signature' => '',
        ]));

        $this->assertFalse($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_fails_if_data_is_missing()
    {
        $request = new Request($this->validParams([
            'Message' => '',
        ]));

        $this->assertFalse($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_calls_the_subscribe_url_when_its_a_subscription_confirmation_requests()
    {
        $request = new Request($this->validParams([
            'Type' => 'SubscriptionConfirmation',
            'SubscribeURL' => url('test-route'),
            'Token' => '',
        ]));

        $this->expectExceptionMessage("file_get_contents(".url('test-route')."): failed to open stream: HTTP request failed! HTTP/1.1 404 Not Found");

        $this->validator->isValid($request, $this->config);
    }
}
