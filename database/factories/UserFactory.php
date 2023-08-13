<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'discord_id' => $this->faker->unique()->numberBetween(1, 1000),
			'discord_access_token' => Str::random(10),
			'discord_token_expires' => now()->addHour(),
			'discord_refresh_token' => Str::random(10),
			'discord_name' => $this->faker->unique()->userName(),
		];
	}
}
