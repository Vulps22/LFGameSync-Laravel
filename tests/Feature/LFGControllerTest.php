<?php

namespace Tests\Feature;

namespace Tests\Feature;

use App\Models\DiscordServer;
use App\Models\Game;
use App\Models\GameUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LFGControllerTest extends TestCase
{
	use RefreshDatabase;

	public function testFindReturnsUsersWithMatchingGameAndServer()
	{
		// Create a game and users with matching game and server
		$game = Game::factory()->create(['name' => 'Overwatch']);
		$user1 = User::factory()->create(['discord_name' => 'User1', 'discord_id' => '123']);
		$user2 = User::factory()->create(['discord_name' => 'User2', 'discord_id' => '456']);
		$user3 = User::factory()->create(['discord_name' => 'User3', 'discord_id' => '789']);
		GameUser::factory()->create(['user_id' => $user1->id, 'game_id' => $game->id]);
		GameUser::factory()->create(['user_id' => $user2->id, 'game_id' => $game->id]);
		GameUser::factory()->create(['user_id' => $user3->id, 'game_id' => $game->id]);

		// Create multiple DiscordServers with different users
		$server1 = DiscordServer::factory()->create(['server_id' => '123', 'user_id' => $user1->id, 'share_library' => 1]);
		$server2 = DiscordServer::factory()->create(['server_id' => '456', 'user_id' => $user2->id, 'share_library' => 1]);
		$server3 = DiscordServer::factory()->create(['server_id' => '789', 'user_id' => $user3->id, 'share_library' => 1]);

		// Make a request to the findFor endpoint with matching game and server
		$response = $this->get('/api/lfg/find?game=Overwatch&server=123');

		// Assert that the response contains the correct user
		$response->assertStatus(200);
		$response->assertJsonCount(1);
		$response->assertJsonFragment(['discord_name' => 'User1']);
		$response->assertJsonFragment(['discord_id' => '123']);
	}

	public function testFindReturnsErrorIfGameNotFound()
	{
		// Make a request to the findFor endpoint with a game that doesn't exist
		$response = $this->get('/api/lfg/find?game=NonexistentGame&server=123');

		// Assert that the response contains an error message
		$response->assertStatus(200);
		$response->assertSee('Game not found');
	}

	public function testFindReturnsErrorIfServerNotFound()
	{
		// Create a game but no server
		$game = Game::factory()->create(['name' => 'Overwatch']);

		// Make a request to the findFor endpoint with a server that doesn't exist
		$response = $this->get('/api/lfg/find?game=Overwatch&server=123');

		// Assert that the response contains an error message
		$response->assertStatus(200);
		$response->assertSee('Server not found');
	}

	public function testFindReturnsErrorIfNoGameNameProvided()
	{
		// Make a request to the findFor endpoint without a game name
		$response = $this->get('/api/lfg/find?server=123');

		// Assert that the response contains an error message
		$response->assertStatus(200);
		$response->assertSee('No game name provided');
	}
}
