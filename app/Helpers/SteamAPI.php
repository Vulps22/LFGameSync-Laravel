<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;


class SteamAPI
{
    protected $apiKey;
    protected $baseUrl = 'https://api.steampowered.com';

    public function __construct()
    {
        $this->apiKey = config('services.steam_api_key');
    }

    public function getPlayerOwnedGames($steamId)
    {
        $url = "{$this->baseUrl}/IPlayerService/GetOwnedGames/v0001/";
        $query = http_build_query([
            'key' => $this->apiKey,
            'steamid' => $steamId,
			'include_appinfo' => '1',
			'include_played_free_games' => '1',
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$query}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // You can add more methods here to interact with different parts of the Steam API.

    public function getUser($steamId)
    {
        $response = Http::get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/", [
            'key' => $this->apiKey,
            'steamids' => $steamId,
        ]);

        $userData = $response->json()['response']['players'][0] ?? null;
        return $userData;
    }

	public function getGame($id){
		$response = Http::get("https://store.steampowered.com/api/appdetails/", [
			'appids' => $id,
		]);

		$gameData = $response->json()[$id]['data'] ?? null;
		return $gameData;
	}
}
