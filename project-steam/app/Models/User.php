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

	public function getSteamIdAttribute($value)
	{
		return $value ?: $this->id;
	}

	public function getDiscordIdAttribute($value)
	{
		return $value ?: $this->id;
	}

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

			$discordServer = DiscordServer::firstOrNew(['server_id' => $server['id']]);
			$discordServer->user_id = $this->id;
			$discordServer->name = $server['name'];
			$discordServer->share_library = false;
			$discordServer->icon_hash = $server['icon'];
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
}
