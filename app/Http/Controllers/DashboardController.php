<?php

namespace App\Http\Controllers;

use App\Models\DiscordServer;
use App\Models\User;

class DashboardController extends Controller
{

	public function index()
	{
		if (!auth()->check()) return redirect('/');

		$this->syncDiscordServers();
		return view('dashboard');
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
		if(!$user) return;
		$user->logoutDiscord();
		auth()->logout();
		
		return redirect('/');
	}
}
