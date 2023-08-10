<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

use \App\Helpers\DiscordAPI;
use \App\Helpers\SteamAPI;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements AuthenticatableContract
{

	use Authenticatable;
	protected $fillable = [
		'steam_id',
		'discord_id'
	];

	protected $casts = [
		'steam_id' => 'string',
		'discord_id' => 'string',
		'discord_token_expires' => 'timestamp'
	];


	public function getDiscordAccessToken()
	{
		return $this->discord_access_token;
	}

	public function setDiscordAccessToken($accessToken, $expires)
	{
		$this->discord_access_token = $accessToken;
		$this->discord_token_expires = $expires;

		$this->save();
	}

	public function getDiscordRefreshToken()
	{
		return $this->discord_refresh_token;
	}

	public function setDiscordRefreshToken($refreshToken)
	{
		$this->discord_refresh_token = $refreshToken;

		$this->save();
	}

	public function getDiscordTokenExpiresAt()
	{
		return $this->discord_token_expires;
	}

	//get the user's discord user object from the Discord API
	public function discordUser()
	{
		$discord = new DiscordAPI();
		return $discord->getUser($this->discord_access_token);
	}

	/**Get a list of the user's servers from discord. Each one should be a DiscordServer*/
	public function syncDiscordServers()
	{
		$discord = new DiscordAPI();
		$servers = $discord->getGuilds($this->discord_access_token);

		foreach ($servers as $server) {

			$discordServer = DiscordServer::firstOrNew(['server_id' => $server['id'], 'user_id' => $this->id]);
			$discordServer->user_id = $this->id;
			$discordServer->name = $server['name'];
			$discordServer->icon_hash = $server['icon'];
			if (!$discordServer->exists()) {
				$discordServer->share_library = false;
			}
			$discordServer->save();
		}

		//remove discord servers that the user is no longer a member of
		$this->discordServers()->whereNotIn('server_id', array_column($servers, 'id'))->delete();
	}

	public function discordServers(): HasMany
	{
		return $this->hasMany(DiscordServer::class);
	}

	public function discordAvatar()
	{
		$discord = new DiscordAPI();
		return $discord->getAvatar($this->discord_id, $this->discordUser()['avatar']);
	}

	public function linkedAccounts()
	{
		return $this->hasOne(GameAccount::class);
	}

	//get the steam user from the Steam API
	public function steamUser()
	{
		$steam = new SteamAPI();
		$accounts = $this->linkedAccounts;

		return $steam->getUser($accounts->steam_id);
	}

	public function syncGames($type)
	{
		switch ($type) {
			case 'Steam':
				$this->syncSteamGames();
				break;
		}
	}

	public function syncSteamGames()
	{
		$steam = new SteamAPI();
		$steamGames = $steam->getPlayerOwnedGames($this->linkedAccounts->steam_id)['response']['games'];

		//add games to database

		foreach($steamGames as $game) {
			$gameModel = Game::firstOrNew(['game_id' => $game['appid']]);
			$gameModel->name = $game['name'];
			$gameModel->image_url = $game['img_icon_url'];
			$gameModel->save();

			//add game to user
			$gameUser = GameUser::firstOrNew(['user_id' => $this->id, 'game_id' => $gameModel->id]);
			$gameUser->save();
		}
	}

	public function games()
	{

		return $this->hasMany(GameUser::class)->with('game');
		
	}

	public function gameCount()
	{
		return $this->games()->count();
	}


}