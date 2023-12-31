<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DiscordAPI
{
	public static function getAuthURL()
	{
		$params = [
			'client_id' => config('services.discord')['client_id'],
			'redirect_uri' => route('discord.callback'),
			'response_type' => 'code',
			'scope' => 'identify guilds'
		];

		return 'https://discordapp.com/api/oauth2/authorize?' . http_build_query($params);
	}

	public static function getAccessToken($code)
	{
		$data = [
			'client_id' => config('services.discord')['client_id'],
			'client_secret' => config('services.discord')['client_secret'],
			'redirect_uri' => route('discord.callback'),
			'grant_type' => 'authorization_code',
			'code' => $code,
		];

		$response = Http::asForm()->post('https://discord.com/api/oauth2/token', $data);

		if (!$response->successful()) return null;

		return $response->json();
	}

	public static function getUser($accessToken)
	{
		$user =  Http::withToken($accessToken)->get('https://discord.com/api/users/@me');
		return $user->json();
	}

	public static function getUserById($id)
	{

		$token = config('services.discord')['bot_token'];
		/*
		$response = Http::withHeaders([
				'Authorization' => "$token"
			])->get("https://discord.com/api/users/$id");
			dump($response->headers());
			error_log(json_encode($response->json()));
			dd($response->json());
			*/

		$url = "https://discord.com/api/users/$id";

		// Create cURL session
		$ch = curl_init($url);

		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Bot ' . $token,
		]);
		// Execute cURL session and get the result
		//$response = curl_exec($ch);

		// Check for cURL errors
		if (curl_errno($ch)) {
			echo 'cURL error: ' . curl_error($ch);
		}

		// Close cURL session
		curl_close($ch);

		$response = '{
			"id": "914368203482890240",
			"username": "vulps23",
			"avatar": "551710b555740ac583bd9d63009fcb65",
			"discriminator": "0",
			"public_flags": 4194368,
			"premium_type": 2,
			"flags": 4194368,
			"banner": "a_e4a2709878c11008cf7ee192ea5c2069",
			"accent_color": 7812399,
			"global_name": "Vulps",
			"avatar_decoration_data": null,
			"banner_color": "#77352f"
		  }
		  ';
		//dd(json_decode($response, true));
		return json_decode($response, true);
	}

	public static function refreshToken($refreshToken)
	{
		$data = [
			'client_id' => env('DISCORD_CLIENT_ID'),
			'client_secret' => env('DISCORD_CLIENT_SECRET'),
			'grant_type' => 'refresh_token',
			'refresh_token' => $refreshToken
		];

		$response = Http::asForm()->post('https://discord.com/api/oauth2/token', $data);

		if ($response->successful()) {
			return $response->json();
		} else {
			return null; // Handle error cases here
		}
	}

	public static function getGuilds($accessToken)
	{
		return Http::withToken($accessToken)->get('https://discord.com/api/users/@me/guilds')->json();
	}

	public static function getAvatar($user_id, $hash)
	{
		return "https://cdn.discordapp.com/avatars/{$user_id}/{$hash}.png";
	}
}
