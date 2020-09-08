<?php

namespace Spatie\MailcoachSesFeedback\Tests;

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
        $request = Request::create('/ses-feedback', 'POST', [], [], [], [], json_encode($this->validParams()));

        $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] = 'SubscriptionConfirmation';

        $this->assertTrue($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_calls_the_subscribe_url_when_its_a_subscription_confirmation_requests()
    {
        $params = $this->getStub('subscriptionConfirmation');
        $params['SubscribeURL'] = url('test-route');

        $request = Request::create('/ses-feedback', 'POST', [], [], [], [], json_encode($params));

        $this->expectExceptionMessage("file_get_contents(".url('test-route').")");

        $this->validator->isValid($request, $this->config);
    }
}
