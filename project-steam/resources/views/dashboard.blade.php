<!-- resources/views/dashboard.blade.php-->

@extends('layouts.app')

@section('content')

<div class="flex max-w-3xl mx-auto">

	<div class="flex-1">
		<h1 class="text-2xl font-bold">{{ Auth()->user()->discordUser()['username'] }}</h1>

		<img src="{{Auth()->user()->discordAvatar()}}" alt="Avatar" class="w-16 h-16 rounded-full">

		<h1>Steam</h1>
		@if(Auth()->user()->steam_id)
		<img src="{{ Auth::user()->steamUser()['avatar'] }}">
		<h4>{{ Auth()->user()->steamUser()['personaname'] }}</h4>
		
		<button class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Sync Games</button>
		@endif

	</div>

	<div class="flex-1">

		<details class="border rounded p-4 mb-4">
			<summary class="font-bold">Linked Discord Servers</summary>

			<div class="mt-2">
				@foreach(Auth()->user()->discordServers as $server)
				<div class="flex justify-between items-center mb-2">
					<div>{{ $server['name'] }}</div>

					<div class="switch-container">
						<input type="checkbox" name="share" class="switch" {{ $server->share_library ? 'checked' : '' }}>
					</div>
				</div>
				@endforeach
			</div>

		</details>

	</div>

</div>

@endsection