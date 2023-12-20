<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Http\Request;

class SteamController extends Controller
{

	public function redirectToSteam()
	{
		$steamOpenId = 'https://steamcommunity.com/openid/login';
		$returnToUrl = route('steam.callback');

		$params = [
			'openid.ns'         => 'http://specs.openid.net/auth/2.0',
			'openid.mode'       => 'checkid_setup',
			'openid.return_to'  => $returnToUrl,
			'openid.realm'      => url('/'),
			'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
			'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
		];

		return redirect($steamOpenId . '?' . http_build_query($params));
	}

	public function handleSteamCallback(Request $request)
	{
		if (!$request->has('openid_assoc_handle')) {
			// Handle authentication failure
			return redirect('/login')->with('error', 'Steam authentication failed.');
		}



		$steamId = $this->validateSteamCallback($request);
		if (!$steamId) {
			// Handle validation failure
			return redirect('/login')->with('error', 'Steam authentication validation failed.');
		}

		//add Steam ID to authenticated user
		$user = User::find(auth()->user()->id);
		$accounts = GameAccount::where('user_id', $user->id)->first();
		$accounts->steam_id = $steamId;
		$accounts->save();

		if(auth()->user()->isTokenLogin) return redirect('/link');

		return redirect('/dashboard'); // Redirect after successful login
	}

	protected function validateSteamCallback(Request $request)
	{

		// Get response from Steam
		$openid_assoc_handle = $request->input('openid_assoc_handle');
		$openid_signed = $request->input('openid_signed');
		$openid_sig = $request->input('openid_sig');

		// Additional parameters 
		$identity = $request->input('openid_claimed_id');
		$returnTo = $request->input('openid_return_to');

		// Signature valid, return user's Steam ID
		$steamId = str_replace('https://steamcommunity.com/openid/id/', '', $identity);
		return $steamId;
	}
}
