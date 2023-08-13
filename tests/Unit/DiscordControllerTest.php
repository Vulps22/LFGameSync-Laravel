<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Http\Controllers\Auth\DiscordController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ReflectionClass;
use Tests\TestCase;


class DiscordControllerTest extends TestCase
{
	/*
	public function testDoLoginWithNoCookies()
	{

		$response = $this->get('/login');

		$response->assertStatus(302);

		$response->assertRedirect('https://discordapp.com/api/oauth2/authorize?client_id=1139301810369204254&redirect_uri=http%3A%2F%2Fgamesync.ajmcallister.co.uk%2Flogin%2Fcallback&response_type=code&scope=identify+guilds');
	}

	public function testDoLoginWithValidCookie()
	{
		if(Cookie::has('discord_token')) Cookie::queue(Cookie::forget('discord_token'));
		// Create a mock object for the DiscordController class
		$mock = $this->partialMock(DiscordController::class, function (MockInterface $mock) {
			// Set up the mock to return true for the cookieLogin() method
			$mock->shouldReceive('cookieLogin')->once()->andReturn(true);
		});

		// Set up expectations for the cookieLogin() method
		//$mockController->shouldReceive('cookieLogin')->once()->andReturn(true);
		//app()->instance(DiscordController::class, $mockController);
		// Call the doLogin() method with a GET request to the /login route with the cookie set
		$response = $this->withCookie("discord_token", "test")->get('/login');

		// Assert that the response redirects to the /dashboard URL
		$response->assertRedirect('/dashboard');
	}
	*/

	public function testValidateToken()
	{
		// Mock the DiscordAPI::getUser() method to return a fake user
		Http::fake([
			'discord.com/api/users/@me' => Http::response([
				'id' => '1234567890',
				'username' => 'test_user',
			], 200),
		]);
	
		// Create an instance of the DiscordController class
		$controller = new DiscordController();
	
		// Use reflection to access the private validateToken() method
		$class = new ReflectionClass($controller);
		$method = $class->getMethod('validateToken');
		$method->setAccessible(true);
	
		// Call the validateToken() method with a valid access token
		$user = $method->invokeArgs($controller, ['valid_access_token']);
	
		// Assert that the user was created and saved to the database
		$this->assertDatabaseHas('users', [
			'discord_id' => '1234567890',
			'discord_name' => 'test_user',
		]);
	
		// Assert that the user's game account was created and saved to the database
		$this->assertDatabaseHas('game_accounts', [
			'user_id' => $user->id,
		]);
	
		// Assert that the user is logged in
		$this->assertTrue(auth()->check());
	}
}
