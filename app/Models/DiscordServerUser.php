<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscordServerUser extends Model
{
	use HasFactory;

	protected $table = 'discord_server_users';

	protected $fillable = [
		'server_id',
		'user_id',
		'share_library'
	];

	public function server()
	{
		return $this->belongsTo(discordServer::class, 'server_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

}
