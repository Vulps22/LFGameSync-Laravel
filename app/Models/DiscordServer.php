<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordServer extends Model
{

	protected $fillable = [
		'user_id',
		'server_id',
		'name',
		'icon_hash',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function getIconAttribute($value)
	{
		return "https://cdn.discordapp.com/icons/{$this->server_id}/{$value}.png";
	}
}
