<?php

namespace Database\Factories;

use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportMessage>
 */
class SupportMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'message' => fake()->randomElement(SupportMessage::ALLOWED_MESSAGE),
            'sender_type' => fake()->randomElement(SupportMessage::ALLOWED_SENDER_TYPE),
            'sent_at' => now(),
            'ticket_id' => SupportTicketFactory::new(),
        ];
    }
}
