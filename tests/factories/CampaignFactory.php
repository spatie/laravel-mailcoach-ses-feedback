<?php

namespace Spatie\MailcoachSesFeedback\Tests\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $this->faker->randomHtml(),
            'status' => CampaignStatus::Draft,
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
            'email_list_id' => new EmailListFactory(),
        ];
    }
}
