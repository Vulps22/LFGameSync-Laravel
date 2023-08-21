<?php

namespace App\Http\Controllers;

use App\Models\DiscordServer;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    public function index()
	{
		if(Cookie::get('discord_token')) return redirect('/login');
		
		//count how many servers are in the database
		$discordCount = DiscordServer::count();
		

		return view('home', [
			'user' => auth()->user(),
			'discordCount' => $discordCount,
			'playerCount' => User::count()
		]);
	}
}
