<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use \App\Helpers\DiscordAPI;
use \App\Helpers\SteamAPI;
use App\Http\Controllers\DiscordController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cookie;

class User extends Model implements AuthenticatableContract
{
	use HasFactory;
	use Authenticatable;

	protected $fillable = [
		'discord_id'
	];

	protected $casts = [
		'discord_id' => 'string',
		'discord_token_expires' => 'timestamp'
	];

	public static function boot()
	{
		parent::boot();

		static::created(function ($model) {
			static::createLinkedAccount($model);
		});
	}


	// Authentication and Tokens
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

	public function logoutDiscord()
	{
		$this->discord_access_token = null;
		$this->discord_refresh_token = null;
		$this->discord_token_expires = null;
		$this->save();
		Cookie::queue(Cookie::forget('discord_token'));
	}

	// Discord Related Methods
	public function discordUser()
	{
		if (!$this->discord_access_token) return false;
		$discord = new DiscordAPI();
		return $discord->getUser($this->discord_access_token);
	}

	public function syncDiscordServers()
	{
		if (!$this->discord_access_token) return redirect('/login');
		$discord = new DiscordAPI();
		$servers = $discord->getGuilds($this->discord_access_token);

		//set should_delete to true for all servers the user is in
		$this->discordServers()->update(['should_delete' => true]);


		foreach ($servers as $server) {
			//if the server exists add a discord_server_user record
			$discordServer = DiscordServer::where(['discord_id' => $server['id']])->first();
			if(!$discordServer) continue;

			$serverUser = DiscordServerUser::firstOrNew(['server_id' => $discordServer->id, 'user_id' => $this->id]);
			if(!$serverUser->exists) {
				$serverUser->share_library = false;
			}

			//this server exists so we don't want to delete it
			$serverUser->should_delete = false;
			$serverUser->save();
			
		}

		//delete all servers that should be deleted
		$this->discordServers()->where('should_delete', true)->delete();
	}

	public function discordAvatar()
	{
		$discord = new DiscordAPI();
		return $discord->getAvatar($this->discord_id, $this->discordUser()['avatar']);
	}

	// Linked Accounts
	public function linkedAccounts()
	{
		return $this->hasOne(GameAccount::class) ?: [];
	}

	private static function createLinkedAccount($model)
	{
		$linkedAccount = new GameAccount();
		$linkedAccount->user_id = $model->id;
		$linkedAccount->save();
	}

	// Steam Related Methods
	public function steamUser()
	{
		$steam = new SteamAPI();
		$accounts = $this->linkedAccounts;
		return $steam->getUser($accounts->steam_id);
	}

	public function syncGames($type = null)
	{
		if(!$type) {
			//sync all game libraries
			$this->syncSteamGames();
		}

		switch ($type) {
			case 'Steam':
				$this->syncSteamGames();
				break;
				// Add more cases for other platforms if needed
		}

		DiscordController::setStat("games", Game::count());
	}

	public function syncSteamGames()
	{
		if(!$this->linkedAccounts) return;
		if(!$this->linkedAccounts->steam_id) return;

		$steam = new SteamAPI();
		$steamGames = $steam->getPlayerOwnedGames($this->linkedAccounts->steam_id)['response'] ?? [];
		if (!$steamGames || !array_key_exists('games', $steamGames)) {
			return;
		}
		$steamGames = $steamGames['games'];

		foreach ($steamGames as $game) {
			$gameModel = Game::firstOrNew(['game_id' => $game['appid']]);
			$gameModel->name = $game['name'];
			$gameModel->image_url = $game['img_icon_url'];
			$gameModel->save();

			$gameUser = GameUser::firstOrNew(['user_id' => $this->id, 'game_id' => $gameModel->id]);
			$gameUser->save();
		}
	}

	public function unlinkSteamGames(){
		$games = GameUser::where('user_id', $this->id)->get();
		foreach($games as $game){
			$game->delete();
		}
	}

	// Relationships
	public function discordServers(): HasMany
	{
		return $this->hasMany(DiscordServerUser::class);
	}

	public function games()
	{
		return $this->belongsToMany(Game::class, 'game_users');
	}

	// Miscellaneous Methods
	public function gameCount()
	{
		return $this->games()->count();
	}
}
