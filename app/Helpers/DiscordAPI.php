<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DiscordAPI
{

	public function getUser($accessToken)
	{
		$user =  Http::withToken($accessToken)->get('https://discord.com/api/users/@me');
		return $user->json();
	}

	public function getGuilds($accessToken)
	{
		return Http::withToken($accessToken)->get('https://discord.com/api/users/@me/guilds')->json();
	}

	public function getAvatar($user_id, $hash)
	{
		return "https://cdn.discordapp.com/avatars/{$user_id}/{$hash}.png";
	}
}
