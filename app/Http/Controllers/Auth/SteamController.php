<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use App\Models\LinkToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class SteamController extends Controller
{

	public function redirectToSteam()
{
    // Get the token from the request or session
    $token = request()->get('token') ?? Cookie::get('oneTimeToken');
    
    if (!$token) {
        return redirect('/link')->withErrors('Token is required for Steam login.');
    }

    $steamOpenId = 'https://steamcommunity.com/openid/login';
    $returnToUrl = route('steam.callback', ['token' => $token]);

    // Include the token in the state parameter
    $params = [
        'openid.ns'         => 'http://specs.openid.net/auth/2.0',
        'openid.mode'       => 'checkid_setup',
        'openid.return_to'  => $returnToUrl,
        'openid.realm'      => url('/'),
        'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
        'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        'openid.state'      => $token, // Pass the token here
    ];

    return redirect($steamOpenId . '?' . http_build_query($params));
}


public function handleSteamCallback(Request $request)
{
    // Retrieve the state parameter (which contains the token)
    $token = $request->query('token');

	if (!$token) {
        return redirect('/link')->with('error', 'Invalid Steam login attempt.');
    }

	// Find the corresponding LinkToken
    $linkToken = LinkToken::where('token', $token)->first();

	if (!$linkToken || $linkToken->isExpired()) {
		return redirect('/link')->with('error', 'The token is invalid or has expired.');
    }

    $steamId = $this->validateSteamCallback($request);

	if (!$steamId) {
		return redirect('/link')->with('error', 'Steam authentication validation failed.');
    }

	// Link Steam ID to the user associated with the token
    $user = User::find($linkToken->user_id);
	$accounts = GameAccount::firstOrCreate(['user_id' => $user->id]);
	$accounts->steam_id = $steamId;
	$accounts->save();

	Auth::login($user); // Log in the user

    return redirect('/link'); // Redirect after successful login
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
