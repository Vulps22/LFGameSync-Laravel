<?php

namespace App\Http\Controllers;

use App\Models\DiscordServer;
use App\Models\DiscordServerUser;
use App\Models\User;

class DashboardController extends Controller
{
	public $discordUserServers;


	public function index()
	{
		if (!auth()->check()) return redirect('/');

		$this->syncDiscordServers();

		$userId = auth()->user()->id;
		$discordUserServers = DiscordServerUser::where('user_id', $userId)
			->join('discord_servers', 'discord_server_users.server_id', '=', 'discord_servers.id')
			->orderBy('discord_servers.name', 'asc')
			->get();

		return view('dashboard', [
			'user' => auth()->user(),
			'discordServers' => $discordUserServers,
		]);
	}

	public static function syncDiscordServers()
	{
		$user = User::find(auth()->user()->id);
		if (!$user) {
			return;
		}

		$user->syncDiscordServers();
	}

	public function logout()
	{
		$user = User::find(auth()->user()->id);
		if (!$user) return;
		$user->logoutDiscord();
		auth()->logout();

		return redirect('/');
	}
}
