<?php

namespace Spatie\MailcoachSesFeedback\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\MailcoachEditor\MailcoachEditorServiceProvider;
use Spatie\MailcoachMailgunFeedback\MailcoachMailgunFeedbackServiceProvider;
use Spatie\MailcoachPostmarkFeedback\MailcoachPostmarkFeedbackServiceProvider;
use Spatie\MailcoachSendgridFeedback\MailcoachSendgridFeedbackServiceProvider;
use Spatie\MailcoachSendinblueFeedback\MailcoachSendinblueFeedbackServiceProvider;
use Spatie\MailcoachSesFeedback\MailcoachSesFeedbackServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\Mailcoach\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        Route::mailcoach('mailcoach');

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            MailcoachMailgunFeedbackServiceProvider::class,
            MailcoachSesFeedbackServiceProvider::class,
            MailcoachSendgridFeedbackServiceProvider::class,
            MailcoachPostmarkFeedbackServiceProvider::class,
            MailcoachSendinblueFeedbackServiceProvider::class,
            MailcoachEditorServiceProvider::class,
            MailcoachServiceProvider::class,
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
        if (! Schema::hasTable('webhook_calls')) {
            $createWebhookCalls = require __DIR__.'/../../../vendor/spatie/laravel-mailcoach/database/migrations/create_webhook_calls_table.php';
            $createWebhookCalls->up();
        }

        if (! Schema::hasTable('mailcoach_campaigns')) {
            $createMailcoachTables = require __DIR__.'/../../../vendor/spatie/laravel-mailcoach/database/migrations/create_mailcoach_tables.php';
            $createMailcoachTables->up();
        }
    }

    public function getStub(string $name): array
    {
        return json_decode(file_get_contents(__DIR__ . "/stubs/{$name}.json"), true);
    }
}
