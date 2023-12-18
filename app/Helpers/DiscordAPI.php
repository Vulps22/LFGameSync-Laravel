<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DiscordAPI
{
	public static function getAuthURL(){
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

	public static function getUserById($id) {

		$token = config('services.discord')['client_secret'];
	
		$response = Http::withHeaders([
				'Authorization' => "$token"
			])->get("https://discord.com/api/users/$id");
			error_log($response->json());
			return $response->json();
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
