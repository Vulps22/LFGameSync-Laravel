<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
	protected $model = Game::class;

	public function definition()
	{
		return [
			'game_id' => $this->faker->unique()->randomNumber(),
			'name' => $this->faker->unique()->word(),
			'image_url' => $this->faker->imageUrl(),
		];
	}
}
