<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ServerCard extends Component
{
	public $server;

	public $share_library = false;

	public function render()
	{
		return view('livewire.server-card');
	}

	public function toggleSharing()
	{
		$this->server->share_library = !$this->server->share_library;
		$this->server->save();
	}
}
