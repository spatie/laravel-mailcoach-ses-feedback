<?php

namespace Spatie\MailcoachSesFeedback\Tests\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'uuid' => $this->faker->uuid,
            'subscribed_at' => now(),
            'email_list_id' => new EmailListFactory(),
        ];
    }
}
