<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\DiscordServer;
use App\Models\DiscordServerUser;

class DiscordServerList extends TestCase
{
	/**
	 * Dashboard should list all servers in the database that the user is a member of
	 */

	public function testDashboardListsServers()
	{
		//create a user
		$user = User::factory()->create();

		//create a server
		$server = DiscordServer::factory()->create();

		//create a server user
		$serverUser = DiscordServerUser::factory()->create([
			'server_id' => $server->id,
			'user_id' => $user->id
		]);

		//login as the user
		$this->actingAs($user);

		//visit the dashboard
		$response = $this->get('/');

		//assert that the server is listed
		$response->assertSee($server->name);
	}
}
