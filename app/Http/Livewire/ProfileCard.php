<?php

namespace App\Http\Livewire;

use App\Models\GameAccount;
use Livewire\Component;

class ProfileCard extends Component
{

	public $name;
	public $avatar;
	public $type;
	public $useGameAccounts = false;
	public $gameAccountId = null;


	public function render()
	{

		switch ($this->type) {
			case 'Steam':
				return view($this->steamView());
				break;
			default:
				return view('livewire.profile-card');
		}
	}

	public function syncServers()
	{
		$this->emit('syncServers');
	}

	public function steamView()
	{

		if (!$this->gameAccountId) return 'livewire.steam-card';

		return 'livewire.profile-card';

	}
}
