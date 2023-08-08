<!-- resources/views/dashboard.blade.php-->

@extends('layouts.app')

@section('content')

<div class="flex flex-col mx-52 mt-10">

	<div class="mx-96 pb-20">
		<div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 pb-10 px-10">
			<h1 class="text-center text-2xl">Profiles</h1>
			<div class="grid grid-cols-2 gap-96">

				<livewire:profile-card name="{{ Auth()->user()->discordUser()['username'] }}" avatar="{{ Auth()->user()->discordAvatar() }}" type="Discord" />

				<livewire:profile-card name="{{ Auth()->user()->steamUser()['personaname'] }}" avatar="{{ Auth()->user()->steamUser()['avatar'] }}" type="Steam" useGameAccounts="true" gameAccountId="{{ Auth()->user()->linkedAccounts->steam_id }}" />

			</div>
		</div>
	</div>
</div>

<div class="grow border rounded p-4 m-4">

	<h2 class="font-bold">Linked Discord Servers</h2>

	<div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
		@foreach(Auth()->user()->discordServers as $server)


		<div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
			<div class="flex flex-col items-center pb-10 pt-5">

				<img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="https://cdn.discordapp.com/icons/{{$server['server_id']}}/{{$server['icon_hash']}}" alt="Bonnie image" />
				<h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $server['name'] }}</h5>

				<div class="flex mt-4 space-x-3 md:mt-6">
					<label class="relative inline-flex items-center cursor-pointer">
						<input type="checkbox" value="" class="sr-only peer">
						<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
					</label>
				</div>

			</div>
		</div>
		@endforeach
	</div>

	</details>

</div>

</div>

@endsection