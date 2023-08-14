<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordServer extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'server_id',
		'name',
		'icon_hash',
		'share_library',
	];

	protected $casts = [
		'share_library' => 'boolean',
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
