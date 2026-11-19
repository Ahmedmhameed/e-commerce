<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'first_name' => mb_substr(fake()->firstName(), 0, 30),
            'last_name' => mb_substr(fake()->lastName(), 0, 44),
            'password_hash' => static::$password ??= Hash::make('password'),
            'phone' => fake()->e164PhoneNumber(),
        ];
    }
}
