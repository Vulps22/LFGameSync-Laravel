<?php
namespace Database\Factories;

use App\Models\DiscordServerUser;
use App\Models\DiscordServer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;




class DiscordServerUserFactory extends Factory
{
	protected $model = DiscordServerUser::class;

	public function definition()
	{
		return [
			'server_id' => DiscordServer::factory()->create()->id,
			'user_id' => User::factory()->create()->id,
			'share_library' => $this->faker->boolean()
		];
	}
}
