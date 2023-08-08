<!-- resources/views/dashboard.blade.php-->

@extends('layouts.app')

@section('content')

<div class="flex flex-col mx-52 mt-10">

	<div class="mx-96 pb-20">
		<div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 pb-10">
			<h1 class="text-center text-2xl">Profiles</h1>
			<div class="flex flex-row">
				<div class="flex flex-col w-1/5 ml-20 border border-gray-700 rounded-lg">
					<div class="mt-5 flex flex-col items-center">

						<div class="flex-shrink-0">
							<img src="{{ Auth()->user()->discordAvatar() }}" class="w-12 h-12 rounded-full">
						</div>

						<div class="w-full items-center">
							<h1 class="font-medium text-center">{{ Auth()->user()->discordUser()['username'] }}</h1>
							<p class="w-full text-center text-sm text-gray-400">Discord</p>
						</div>
					</div>

					<div class="mx-5 mb-5 flex flex-col">
						<button class="w-full text-blue-500 border border-blue-500 px-4 py-2 rounded mt-4">Sync Servers</button>
						<button class="btn border border-red-700 text-red-700 px-4 py-2 rounded mt-4">Logout</button>
					</div>
				</div>

				<div class="flex flex-col w-1/5 mr-20 ml-auto border border-gray-700 rounded-lg">
					<div class="mt-5 flex flex-col items-center">
						<div class="flex-shrink-0">
							@if (Auth::user()->linkedAccounts->steam_id)
							<img src="{{ Auth::user()->steamUser()['avatar'] }}" class="w-12 h-12 rounded-full">
							@else
							<img src="{{ asset('img/steam_icon_logo.png') }}" class="w-12 h-12 rounded-full">
							@endif
						</div>

						<div>
							@if (Auth::user()->linkedAccounts->steam_id)
							<h1 class="font-medium">{{ Auth()->user()->steamUser()['personaname'] }}</h1>
							@else
							<h1 class="font-medium">Not Linked</h1>
							@endif
							<p class="w-full text-center text-sm text-gray-400">Steam</p>

						</div>
					</div>

					<div class="mx-5 mb-5 flex flex-col">
						@if (Auth::user()->linkedAccounts->steam_id)
						<button class="w-full border border-blue-500 text-blue-500 px-4 py-2 rounded mt-4">Sync Games</button>
						<button class="btn border border-red-700 text-red-700 px-4 py-2 rounded mt-4">Logout</button>
						@else
						<a href="/link/steam" class=" mx-20 h-9 text-white px-4 py-2 rounded mt-4" style="background-image: url(' {{ asset('img/steam_01.png') }} '); background-size:cover;"></a>
						@endif
					</div>
				</div>
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