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
    // Retrieve the bot token from the configuration
    $token = config('services.discord')['bot_token'];
    
    // Define the Discord API URL for fetching user details
    $url = "https://discord.com/api/users/$id";
    
    try {
        // Send an HTTP GET request to the Discord API using the Laravel HTTP client
        $response = Http::withHeaders([
            'Authorization' => "Bot $token",
        ])->get($url);

        // Check if the response is successful
        if ($response->successful()) {


            // Return the JSON-decoded response data
            return $response->json();
        } else {
            // Handle the case where the response was not successful
            error_log("Error fetching user data: " . $response->status());
            return null; // or handle the error as needed
        }
    } catch (\Exception $e) {
        // Log any exceptions that occur during the API request
        error_log("Exception occurred: " . $e->getMessage());
        return null; // or handle the exception as needed
    }
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
