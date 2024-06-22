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
    // Debugging: Initial entry
    DiscordController::sendMessage("log", "Entering index method");

    if (!auth()->check()) {
        // Debugging: Auth check failed
        DiscordController::sendMessage("log", "User has failed auth->check()");
        return redirect('/');
    }

    // Debugging: Auth check passed
    DiscordController::sendMessage("log", "User passed auth->check()");

    if (auth()->user()->isTokenLogin) {
        // Debugging: User is a token login
        DiscordController::sendMessage("log", "User is token login, redirecting to '/'");
        return redirect('/');
    }

    // Debugging: User is not a token login
    DiscordController::sendMessage("log", "User is not token login");

    // Debugging: Starting syncDiscordServers
    DiscordController::sendMessage("log", "Starting syncDiscordServers");
    $this->syncDiscordServers();
    // Debugging: Finished syncDiscordServers
    DiscordController::sendMessage("log", "Finished syncDiscordServers");

    $userId = auth()->user()->id;

    // Debugging: Retrieving discord servers for user
    DiscordController::sendMessage("log", "Retrieving discord servers for user ID: " . $userId);
    $discordUserServers = DiscordServerUser::where('user_id', $userId)
        ->join('discord_servers', 'discord_server_users.server_id', '=', 'discord_servers.id')
        ->orderBy('discord_servers.name', 'asc')
        ->get();

    // Debugging: Retrieved discord servers
    DiscordController::sendMessage("log", "Retrieved discord servers for user ID: " . $userId);

    // Debugging: Returning view
    DiscordController::sendMessage("log", "Returning dashboard view for user ID: " . $userId);

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
