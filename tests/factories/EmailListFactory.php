<?php

namespace Spatie\MailcoachSesFeedback\Tests\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListFactory extends Factory
{
    protected $model = EmailList::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'uuid' => $this->faker->uuid,
            'default_from_email' => $this->faker->email,
            'default_from_name' => $this->faker->name,
            'default_reply_to_email' => $this->faker->email,
            'default_reply_to_name' => $this->faker->name,
            'campaign_mailer' => config('mail.default') ?? 'array',
            'transactional_mailer' => config('mail.default') ?? 'array',
        ];
    }
}
