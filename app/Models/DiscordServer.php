<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordServer extends Model
{
	use HasFactory;

	protected $fillable = [
		'discord_id',
		'name',
		'icon_hash',
	];

	protected $casts = [
		'share_library' => 'boolean',
	];

	public function getIconAttribute($value)
	{
		return "https://cdn.discordapp.com/icons/{$this->discord_id}/{$value}.png";
	}
}
