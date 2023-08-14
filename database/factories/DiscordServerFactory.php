<?php

namespace Database\Factories;

use App\Models\DiscordServer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscordServerFactory extends Factory
{
    protected $model = DiscordServer::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'server_id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->unique()->word(),
            'icon_hash' => $this->faker->md5(),
            'share_library' => $this->faker->boolean(),
        ];
    }
}