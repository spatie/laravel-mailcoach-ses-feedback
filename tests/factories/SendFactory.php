<?php

namespace Spatie\MailcoachSesFeedback\Tests\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class SendFactory extends Factory
{
    protected $model = Send::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'campaign_id' => new CampaignFactory(),
            'subscriber_id' => new SubscriberFactory(),
        ];
    }
}
