<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProfileCard extends Component
{

	public $name;
	public $avatar;
	public $type;
	public $useGameAccounts = false;
	public $gameAccountId = null;
	public $gameCount = 0;


	public function mount()
	{
		
		if($this->type == 'Steam') {
			$this->gameAccountId = Auth()->user()->linkedAccounts->steam_id;
			if(!$this->gameAccountId) return;

			$this->useGameAccounts = true;
			$steamUser = Auth()->user()->steamUser();
			$this->name = $steamUser['personaname'];
			$this->avatar = $steamUser['avatar'];
			$this->gameCount = Auth()->user()->gameCount();
		}

		if($this->type == 'Discord') {
			$discordUser = Auth()->user()->discordUser();
			$this->name = $discordUser['username'];
			$this->avatar = Auth()->user()->discordAvatar();
		}
	}

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

	public function syncGames()
	{
		Auth()->user()->syncGames($this->type);
		$this->gameCount = Auth()->user()->gameCount();
		

	}

	public function steamView()
	{

		if (!$this->gameAccountId) return 'livewire.steam-card';

		return 'livewire.profile-card';

	}
}
