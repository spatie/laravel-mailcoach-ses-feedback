<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use CreateMailcoachTables;
use CreateWebhookCallsTable;
use CreateMailcoachSesTables;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\BladeX\BladeXServiceProvider;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\MailcoachSesFeedback\MailcoachSesFeedbackServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../vendor/spatie/laravel-mailcoach/database/factories');

        Route::mailcoach('mailcoach');

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            MailcoachServiceProvider::class,
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
        (new CreateMailcoachTables())->up();

        include_once __DIR__ . '/../database/migrations/create_mailcoach_ses_tables.php.stub';
        (new CreateMailcoachSesTables())->up();
    }

    public function getStub(string $name): array
    {
        return json_decode(file_get_contents(__DIR__ . "/stubs/{$name}.json"), true);
    }
}
