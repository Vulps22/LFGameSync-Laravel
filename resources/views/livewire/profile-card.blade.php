<div class="flex flex-col w-full border border-gray-700 rounded-lg">
	<div class="mt-5 flex flex-col items-center">

		<div class="flex-shrink-0">
			<img src="{{ $avatar }}" class="w-12 h-12 rounded-full">
		</div>

		<div class="w-full items-center">
			<h1 class="font-medium text-center">{{ $name }}</h1>
			<p class="w-full text-center text-sm text-gray-400">{{ $type }}</p>
			@if($useGameAccounts)
			<p class="w-full text-center text-sm text-gray-400">{{ $gameCount }} games</p>
			@else
			<p class="w-full text-center text-sm text-gray-400">&nbsp;</p>
			@endif

		</div>
	</div>

	<div class="mx-5 mb-5 flex flex-col">
		@if($useGameAccounts)
		<button class="w-full text-blue-500 border border-blue-500 px-4 py-2 rounded mt-4" wire:click="syncGames" wire:loading.remove>Sync Games</button>
		<div class="w-full text-blue-500 border border-blue-500 px-4 py-2 rounded mt-4 text-center" wire:loading> Syncing Games...</div>
		@else
		<div class="px-4 py-2 mt-4">&nbsp;</div>
		@endif
		<button class="btn border border-red-700 text-red-700 px-4 py-2 rounded mt-4">{{ ($useGameAccounts ? 'Disconnect' : 'Logout') }}</button>
	</div>
</div>