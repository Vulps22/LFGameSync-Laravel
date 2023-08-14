<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameUserFactory extends Factory
{
	protected $model = GameUser::class;

	public function definition()
	{
		return [
			'user_id' => User::factory(),
			'game_id' => Game::factory(),
		];
	}
}
