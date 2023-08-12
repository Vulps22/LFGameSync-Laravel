<?php

namespace App\Http\Controllers;

use App\Models\DiscordServer;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    public function index()
	{
		if(Cookie::get('discord_token')) return redirect('/login/discord');
		
		//count how many servers have share_library enabled by at least one user
		$discordCount = DiscordServer::whereHas('user', function ($query) {
			$query->where('share_library', true);
		})->count();
		

		return view('home', [
			'user' => auth()->user(),
			'discordCount' => $discordCount,
			'playerCount' => User::count()
		]);
	}
}
