<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;


class DiscordController extends Controller
{

	public function redirectToDiscord()
	{

		$params = [
			'client_id' => env('DISCORD_CLIENT_ID'),
			'redirect_uri' => route('discord.callback'),
			'response_type' => 'code',
			'scope' => 'identify guilds'
		];

		return redirect('https://discordapp.com/api/oauth2/authorize?' . http_build_query($params));
	}

	public function handleDiscordCallback(Request $request)
	{

		$code = $request->input('code');

		// Validate state parameter
		$accessTokenResponse = Http::asForm()->post("https://discordapp.com/api/oauth2/token", [
			'client_id' => env('DISCORD_CLIENT_ID'),
			'client_secret' => env('DISCORD_CLIENT_SECRET'),
			'redirect_uri' => route('discord.callback'),
			'grant_type' => 'authorization_code',
			'code' => $code,
		])->json();

		$accessToken = $accessTokenResponse['access_token'];
		$refreshToken = $accessTokenResponse['refresh_token'];
		$expires_at = now()->addSeconds($accessTokenResponse['expires_in']);

		if (!$accessToken) dd('No access token');

		$discord = new \App\Helpers\DiscordAPI;

		$discordUser = $discord->getUser($accessToken);

		// Create/update user
		$user = User::firstOrNew(['discord_id' => $discordUser['id']]);
		$user->discord_name = $discordUser['username'];
		$user->setDiscordAccessToken($accessToken, $expires_at);
		$user->setDiscordRefreshToken($refreshToken);
		$user->save();
		$gameAccount = GameAccount::firstOrNew(['user_id' => $user->id]);
		$gameAccount->user_id = $user->id;
		$gameAccount->save();
		Auth::login($user);

		return redirect('/dashboard');
	}
}
