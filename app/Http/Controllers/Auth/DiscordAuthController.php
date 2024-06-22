<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\DiscordAPI;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

/**
 * This controller handles user authentication via Discord for the application. 
 * It manages the login process, redirects to Discord for authentication, handles 
 * the callback from Discord, and manages tokens (access and refresh tokens) for user sessions. 
 * It includes methods for validating tokens, logging in with cookies, and refreshing tokens.
*/

class DiscordAuthController extends Controller
{

	public function doLogin()
	{

		DiscordController::sendMessage("log", "Cookie Login Begin");
		//check if user is already logged in
		if (Auth::check()){
			DiscordController::sendMessage("log", "Already Logged In Redirecting to Dashboard");
			return redirect('/dashboard');
		}
		
		//if user has the cookie try to log them in
		$discordToken = Cookie::get('discord_token');
		if ($discordToken) {
			if ($this->cookieLogin()) {
				DiscordController::sendMessage("log", "Cookie Login Success");

				return redirect('/dashboard');
			}
			DiscordController::sendMessage("log", "Cookie Was Found but Login Failed");
			Cookie::queue(Cookie::forget('discord_token'));
			$response = redirect('/');
			return $response;
		} else return $this->redirectToDiscord();
	}

	public function redirectToDiscord()
	{
		return redirect(DiscordAPI::getAuthURL());
	}


	public function handleDiscordCallback(Request $request)
	{
		$code = $request->input('code');
		if (!$code) return redirect('/');


		$accessTokenResponse = DiscordAPI::getAccessToken($code);
		if (!$accessTokenResponse) return redirect('/');


		$accessToken = $accessTokenResponse['access_token'];
		$refreshToken = $accessTokenResponse['refresh_token'];
		$expires_at = now()->addSeconds($accessTokenResponse['expires_in']);

		if (!$accessToken) dd('No access token');


		$user = $this->validateToken($accessToken, $refreshToken, $expires_at);

		if ($user) {

			Cookie::queue('discord_token', $accessToken, $accessTokenResponse['expires_in']);

			return redirect('/dashboard');
		} else {
			return redirect('/');
		}
	}

	private function validateToken($accessToken, $refreshToken = null, $expires_at = null)
	{
		$discordUser = DiscordAPI::getUser($accessToken);
		if (!$discordUser) {
			return false;
		}

		$user = User::firstOrNew(['discord_id' => $discordUser['id']]);
		$user->discord_name = $discordUser['username'];

		if ($refreshToken && $expires_at) {
			$user->setDiscordAccessToken($accessToken, $expires_at);
			$user->setDiscordRefreshToken($refreshToken);
		}

		$user->save();

		Auth::login($user);

		return $user;
	}


	/**
	 * If the user has a cookie, try to log them in
	 * If the function returns false ensure you delete the cookie
	 * @return Bool
	 */
	private function cookieLogin(): Bool
	{
		if (Auth::check()) return true;
		$discordToken = Cookie::get('discord_token');
		$user = User::where('discord_access_token', $discordToken)->first();

		if (!$user) return false;
		//if the token has expired, redirect them home
		if (!$user->discord_token_expires_at < now()) {
			return false;
		}
		return $this->refreshToken($user);
	}

	/**
	 * Refresh the user's access token
	 * if the function returns false Access was Denied, the saved token information has been nullified, and the login should be voided
	 * @param User $user
	 * @return Bool
	 */
	private function refreshToken($user): Bool
	{
		$refreshToken = $user->discord_refresh_token;
		$accessTokenResponse = DiscordAPI::refreshToken($refreshToken);
		if (!$accessTokenResponse) {
			$user->setDiscordAccessToken(null, null);
			$user->setDiscordRefreshToken(null);
			$user->save();
			return false;
		}

		$accessToken = $accessTokenResponse['access_token'];
		$refreshToken = $accessTokenResponse['refresh_token'];

		$expires_at = now()->addSeconds($accessTokenResponse['expires_in']);
		$user->setDiscordAccessToken($accessToken, $expires_at);
		$user->setDiscordRefreshToken($refreshToken);
		$user->save();
		Auth::login($user);
		Cookie::queue('discord_token', $accessToken, $accessTokenResponse['expires_in']); //THIS is why testing is important! I mised this and would never have noticed without testing!
		return true;
	}
}
