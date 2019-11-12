<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use CreateMailCoachTables;
use CreateWebhookCallsTable;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\BladeX\BladeXServiceProvider;
use Spatie\Mailcoach\MailCoachServiceProvider;
use Spatie\MailcoachSesFeedback\MailcoachSesFeedbackServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../vendor/spatie/laravel-mailcoach/tests/database/factories');

        Route::mailcoach('mailcoach');

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            MailCoachServiceProvider::class,
            MailcoachSesFeedbackServiceProvider::class,
            BladeXServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('mail.driver', 'log');
    }

    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../vendor/spatie/laravel-webhook-client/database/migrations/create_webhook_calls_table.php.stub';
        (new CreateWebhookCallsTable())->up();

        include_once __DIR__ . '/../vendor/spatie/laravel-mailcoach/database/migrations/create_mailcoach_tables.php.stub';
        (new CreateMailCoachTables())->up();
    }

    public function getStub(): array
    {
        return [
            "Type" => "Notification",
            "MessageId" => "d243eac4-eaf9-512c-b15d-bcfeee0a41d5",
            "TopicArn" => "arn:aws:sns:us-east-2:773197656946:Bounces",
            "Message" => "{
    \"notificationType\": \"Bounce\",
    \"mail\": {
        \"timestamp\":\"2018-10-08T14:05:45 +0000\",
        \"messageId\":\"000001378603177f-7a5433e7-8edb-42ae-af10-f0181f34d6ee-000000\",
        \"source\":\"sender@example.com\",
        \"sourceArn\": \"arn:aws:ses:us-west-2:888888888888:identity/example.com\",
        \"sourceIp\": \"127.0.3.0\",
        \"sendingAccountId\":\"123456789012\",
        \"destination\":[
            \"recipient@example.com\"
        ],
        \"headersTruncated\":false,
        \"headers\":[
            {
                \"name\":\"From\",
                \"value\":\"\\\"Sender Name\\\" <sender@example.com>\"
            },
            {
                \"name\":\"To\",
                \"value\":\"\\\"Recipient Name\\\" <recipient@example.com>\"
            },
            {
                \"name\":\"Message-ID\",
                \"value\":\"custom-message-ID\"
            },
            {
                \"name\":\"Subject\",
                \"value\":\"Hello\"
            },
            {
                \"name\":\"Content-Type\",
                \"value\":\"text/plain; charset=\\\"UTF-8\\\"\"
            },
            {
                \"name\":\"Content-Transfer-Encoding\",
                \"value\":\"base64\"
            },
            {
                \"name\":\"Date\",
                \"value\":\"Mon, 08 Oct 2018 14:05:45 +0000\"
            }
        ],
        \"commonHeaders\":{
            \"from\":[
                \"Sender Name <sender@example.com>\"
            ],
            \"date\":\"Mon, 08 Oct 2018 14:05:45 +0000\",
            \"to\":[
                \"Recipient Name <recipient@example.com>\"
            ],
            \"messageId\":\" custom-message-ID\",
            \"subject\":\"Message sent using Amazon SES\"
        }
    },
    \"bounce\": {
        \"bounceType\":\"Permanent\",
        \"bounceSubType\": \"General\",
        \"bouncedRecipients\":[
            {
                \"status\":\"5.0.0\",
                \"action\":\"failed\",
                \"diagnosticCode\":\"smtp; 550 user unknown\",
                \"emailAddress\":\"recipient1@example.com\"
            },
            {
                \"status\":\"4.0.0\",
                \"action\":\"delayed\",
                \"emailAddress\":\"recipient2@example.com\"
            }
        ],
        \"reportingMTA\": \"example.com\",
        \"timestamp\":\"2012-05-25T14:59:38.605Z\",
        \"feedbackId\":\"000001378603176d-5a4b5ad9-6f30-4198-a8c3-b1eb0c270a1d-000000\",
        \"remoteMtaIp\":\"127.0.2.0\"
    }
}
",
            "Timestamp" => "2019-11-04T10:51:31.683Z",
            "SignatureVersion" => "1",
            "Signature" => "izs8y9RSOZi3WZ/N1KO3yeJvNJbu6Y8u7arYAT8mHFmGFFXUWPKWXwhPfVuz4BJ745OVAMpoMqCZjrbdv82LuCY289L4BZSiVdsZy1A+NbL8QzWH9CEjmsXcFwTC7oroXiQsu5SvAUwa5DijGclMd23ZzwtEWJwc0CqWDuqPr9tuCd6OUz6NMniP/V0LdwgcGZb7pS/eC3pB/+SQX8cUPW+idyyDbCdd6ApS06S/X8wh3vNyilFBN+SeDAaO/BCylzH+xEzJ/OVD8+NUNqGxGCJExE7ETbyuy5z2FTAaBWvepl6/FEVfiDOE4CkU9nQHTZYIlJLT1CsWqckQFrHtdw==",
            "SigningCertURL" => "https://sns.us-east-2.amazonaws.com/SimpleNotificationService-6aad65c2f9911b05cd53efda11f913f9.pem",
            "UnsubscribeURL" => "https://sns.us-east-2.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-east-2:773197656946:Bounces:e2afb1f8-f623-454b-a40a-c5c9b6188173"
        ];
    }
}
