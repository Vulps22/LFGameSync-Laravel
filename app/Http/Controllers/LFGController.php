<?php

namespace App\Http\Controllers;

use App\Http\Resources\LFGResource;
use App\Models\GameUser;
use App\Models\User;
use App\Models\Game;
use App\Models\DiscordServer;

use Illuminate\Http\Request;

class LFGController extends Controller
{
	/**
	 * Find users who own a game
	 */
	public function find(Request $request)
	{
		$gameName = $request->input('game');
		$server_id = $request->input('server');
		if (!$gameName) return "No game name provided";
		$game = Game::where('name', $gameName)->first();
		if (!$game) return "Game not found";

		$server = DiscordServer::where('server_id', $server_id)->first();
		if (!$server) return "Server not found";

		$users = User::select('users.id', 'users.discord_name, users.discord_id')
			->join('game_accounts', 'users.id', '=', 'game_accounts.user_id')
			->join('game_users', 'users.id', '=', 'game_users.user_id')
			->join('discord_servers', 'users.id', '=', 'discord_servers.user_id')
			->join('games', 'game_users.game_id', '=', 'games.id')
			->where('games.id', $game->id)
			->where('discord_servers.server_id', $server_id)
			->where('discord_servers.share_library', 1)
			->get();

		return LFGResource::collection($users);
	}
}
