<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class dashboard extends Component
{
	/**
	 * Create a new component instance.
	 */
	public function __construct()
	{
	}

	public static function syncDiscordServers()
	{
		$user = User::find(auth()->user()->id);
		if (!$user) {
			return;
		}

		$user->syncDiscordServers();
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.dashboard');
	}
}
