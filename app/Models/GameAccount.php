<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameAccount extends Model
{
	use HasFactory;
	protected $fillable = [
		'user_id',
		'steam_id',
		'syncing',
		'last_sync',
	];

	protected $casts = [
		'last_sync' => 'date',
	];

	public function isSyncing($isSyncing = true)
	{
		if(!is_bool($isSyncing)) throw new Exception('Type of $isSyncing MUST be a boolean value');

		if ($isSyncing) {
			$this->syncing = true;
			$this->save();
		} else {
			$this->syncing = false;
			$this->last_sync = now();
			$this->save();
		}
	}

	
}
