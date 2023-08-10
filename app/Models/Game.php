<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

	//add information from the games migration
	protected $fillable = [
		'game_id',
		'name',
		'image_url'
	];
	
	public function users()
	{
		return $this->belongsToMany(User::class, 'user_games');
	}

	public function userCount()
	{
		return $this->users()->count();
	}

	

}
