<?php

namespace App\Http\Controllers;

use App\Http\Resources\LFGResource;
use App\Models\GameUser;
use App\Models\User;
use App\Models\Game;
use App\Models\DiscordServer;
use App\Models\DiscordServerUser;
use Exception;
use Illuminate\Http\Request;

class LFGController extends Controller
{
	/**
	 * Find users who own a game
	 */
	public function find(Request $request)
	{

		$gameName = $request->input('game');
		if (!$gameName) return "No game name provided";

		$user_id = $request->input('user_id'); //the user's discord ID
		if (!$user_id) return "No user ID provided";

		$server_id = $request->input('server');

		$game = Game::where('name', $gameName)->first();
		if (!$game) return "Game not found";

		$user = User::where('discord_id', $user_id)->first();
		if (!$user) return "User not found";

		$user->syncGames();

		$server = DiscordServer::where('discord_id', $server_id)->first();
		if (!$server) return "Server not found";

		$discordServerUser = $user->discordServers()->where('server_id', $server->id)->first();
		if (!$discordServerUser) return "Server User not Registered";

		//if the user is not sharing their library with this server
		if (!$discordServerUser->share_library) return "Not Sharing";

		$users = User::select('users.id', 'users.discord_name', 'users.discord_id')
			->join('game_accounts', 'users.id', '=', 'game_accounts.user_id')
			->join('game_users', 'users.id', '=', 'game_users.user_id')
			->join('discord_server_users', 'users.id', '=', 'discord_server_users.user_id')
			->join('discord_servers', 'discord_server_users.server_id', '=', 'discord_servers.id')
			->join('games', 'game_users.game_id', '=', 'games.id')
			->where('games.id', $game->id)
			->where('discord_servers.id', $server_id)
			->where('discord_server_users.share_library', 1)
			->get() ?? [];

		return LFGResource::collection($users);
	}

	/**
	 * Register a server to the database
	 */
	public function register_server(Request $request)
	{
		$server_id = $request->input('server_id');
		$server_name = $request->input('server_name');
		$icon_hash = $request->input('icon_hash');

		if (!$server_id) return "No server ID provided";

		$server = DiscordServer::firstOrNew(['discord_id' => $server_id]);

		if ($server->name !== $server_name) $server->name = $server_name;
		if ($server->icon_hash !== $icon_hash) $server->icon_hash = $icon_hash;
		$server->save();

		DiscordController::setStat('servers', DiscordServer::count());

		return $server->id;
	}

	/**
	 * Remove Server from list
	 * remove server from user
	 */
	public function remove_server(Request $request)
	{
		try {
			$server_id = $request->input('server_id');
			$server = DiscordServer::where('discord_id', $server_id)->first();
			if (!$server) return "Nothing to remove";

			$id = $server->id;

			$users = DiscordServerUser::where('server_id', $id)->get();

			foreach ($users as $user) {
				$user->delete();
			}

			$server->delete();
			DiscordController::setStat('servers', DiscordServer::count());

			return "Server Removed Successfully";
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}
