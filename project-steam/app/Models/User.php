<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

use \App\Helpers\DiscordAPI;
use \App\Helpers\SteamAPI;

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
    public function discordUser(){
      $discord = new DiscordAPI();
      return $discord->getUser($this->discord_access_token);
    }

    //get the steam user from the Steam API
    public function steamUser(){
      $steam = new SteamAPI();
      return $steam->getUser($this->steam_id);
    }
}