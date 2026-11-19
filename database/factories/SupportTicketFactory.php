<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'priority' => fake()->randomElement(SupportTicket::ALLOWED_PRIORITY),
            'status' => fake()->randomElement(SupportTicket::ALLOWED_STATUS),
            'subject' => fake()->randomElement(SupportTicket::ALLOWED_SUBJECT),
            'user_id' => UserFactory::new(),
        ];
    }
}
