<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;


class LoginTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	public function testLoginWithNewDiscord()
	{

		$discord_access_token = Str::random(10);
		$discord_id = $this->faker->unique()->numberBetween(1, 1000);

		$user = User::factory()->create();

		// Mock the HTTP request
		Http::fake([
			'discord.com/api/oauth2/token' => Http::response([
				'access_token' => $discord_access_token,
				'refresh_token' => Str::random(10),
				'expires_in' => 3600,
			], 200),
			'discord.com/api/users/@me' => Http::response([
				'id' => $discord_id,
				'username' => 'new_test_user',
				'avatar' => '00000000000000000',
				'discriminator' => '0',
				'public_flags' => 64,
				'flags' => 64,
				'banner' => null,
				'accent_color' => 7812399,
				'global_name' => 'test_user',
				'avatar_decoration' => null,
				'banner_color' => '#77352f',
				'mfa_enabled' => true,
				'locale' => 'en-GB',
				'premium_type' => 0,
			], 200),
		]);

		// Visit the homepage
		$response = $this->get('/');

		// Assert that the response is successful
		$response->assertStatus(200, 'Failed to load homepage');

		// Simulate a redirect to the Discord authorization page
		$response = $this->get('/login');

		// Get the app URL from the config file
		$appUrl = config('app.url');

		// Build the redirect URI with the app URL
		$redirectUri = urlencode("$appUrl/login/callback");

		// Assert that the response is a redirect to the Discord authorization page
		$response->assertRedirect("https://discordapp.com/api/oauth2/authorize?client_id=1139301810369204254&redirect_uri=$redirectUri&response_type=code&scope=identify+guilds", 'Failed to redirect to Discord authorization page');

		// Simulate a callback from Discord with a valid access token
		$response = $this->get('/login/callback?code=valid_code');

		// Assert that the response is a redirect to the dashboard page
		$response->assertRedirect('/dashboard', 'Failed to redirect to dashboard page');

		
		//var_dump(Auth::user()->toArray());

		$this->assertDatabaseHas('users', [
			'discord_id' => $discord_id,
			'discord_access_token' => $discord_access_token,
		]);

		$newUser = User::where('discord_id', $discord_id)->first();

		// Assert that the user is authenticated
		$this->assertAuthenticatedAs($newUser);

		// Assert that the new user is not the same as $user
		$this->assertNotEquals($user->id, $newUser->id, 'New user is the same as old user');

		// Assert that the discord_token cookie was set
		$response->assertCookie('discord_token', $discord_access_token);
	}

	public function testLoginWithDiscord()
	{

		$user = User::factory()->create();

		// Mock the HTTP request
		Http::fake([
			'discord.com/api/oauth2/token' => Http::response([
				'access_token' => $user->discord_access_token,
				'refresh_token' => 'valid_refresh_token',
				'expires_in' => 3600,
			], 200),
			'discord.com/api/users/@me' => Http::response([
				'id' => $user->discord_id,
				'username' => 'test_user',
				'avatar' => '00000000000000000',
				'discriminator' => '0',
				'public_flags' => 64,
				'flags' => 64,
				'banner' => null,
				'accent_color' => 7812399,
				'global_name' => 'test_user',
				'avatar_decoration' => null,
				'banner_color' => '#77352f',
				'mfa_enabled' => true,
				'locale' => 'en-GB',
				'premium_type' => 0,
			], 200),
		]);

		// Visit the homepage
		$response = $this->get('/');

		// Assert that the response is successful
		$response->assertStatus(200);

		// Simulate a redirect to the Discord authorization page
		$response = $this->get('/login');

		// Get the app URL from the config file
		$appUrl = config('app.url');

		// Build the redirect URI with the app URL
		$redirectUri = urlencode("$appUrl/login/callback");

		// Assert that the response is a redirect to the Discord authorization page
		$response->assertRedirect("https://discordapp.com/api/oauth2/authorize?client_id=1139301810369204254&redirect_uri=$redirectUri&response_type=code&scope=identify+guilds", 'Failed to redirect to Discord authorization page');

		// Simulate a callback from Discord with a valid access token
		$response = $this->get('/login/callback?code=valid_code');

		// Assert that the response is a redirect to the dashboard page
		$response->assertRedirect('/dashboard');

		// Assert that the user is authenticated
		$this->assertAuthenticatedAs($user);

		
		// Assert that the discord_token cookie was set
		$response->assertCookie('discord_token', $user->discord_access_token);
	}

	public function testLoginWithCookie()
	{
		$user = User::factory()->create();
	
		// Mock the HTTP request
		Http::fake([
			'discord.com/api/oauth2/token' => Http::response([
				'access_token' => $user->discord_access_token,
				'refresh_token' => 'valid_refresh_token',
				'expires_in' => 3600,
			], 200),
			'discord.com/api/users/@me' => Http::response([
				'id' => $user->discord_id,
				'username' => 'test_user',
				'avatar' => '00000000000000000',
				'discriminator' => '0',
				'public_flags' => 64,
				'flags' => 64,
				'banner' => null,
				'accent_color' => 7812399,
				'global_name' => 'test_user',
				'avatar_decoration' => null,
				'banner_color' => '#77352f',
				'mfa_enabled' => true,
				'locale' => 'en-GB',
				'premium_type' => 0,
			], 200),
		]);

		// Visit the homepage
		$response = $this->withCookie('discord_token', $user->discord_access_token)->get('/login');
	
		// Assert that the response is successful
		$response->assertStatus(302);

		// Assert that the response is a redirect to the dashboard page
		$response->assertRedirect('/dashboard');

		// Assert that the user is authenticated
		$this->assertAuthenticatedAs($user);
		
		// Assert that the discord_token cookie was set
		$response->assertCookie('discord_token', $user->discord_access_token);
	}

	public function testLoginWithInvalidCookie()
	{
		User::factory()->create();

		// Visit the homepage
		$response = $this->withCookie('discord_token', "invalid_cookie")->get('/login');
	
		// Assert that the response is successful
		$response->assertStatus(302);

		// Assert that the response is a redirect to the dashboard page
		$response->assertRedirect('/');

		// Assert that the user is authenticated
		$this->assertGuest();
		
		// Assert that the discord_token cookie was set
		$response->assertCookie('discord_token', null);
	}
}
