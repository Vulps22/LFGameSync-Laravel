<!-- resources/views/dashboard.blade.php-->

@extends('layouts.app')

@section('content')

<div class="flex flex-col mx-5 md:mx-52 mt-10">

	<div class="!mx-0 xl:mx-96 pb-20">
		<div class="w-full bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 pb-10 px-10">
			<h1 class="text-center text-2xl">Profiles</h1>
			<div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-2 xl:gap-96 2xl:gap-[40rem]">

				<livewire:profile-card type="Discord"/>

				<livewire:profile-card type="Steam" useGameAccounts="true"/>

			</div>
		</div>
	</div>
</div>

<div class="grow border rounded p-4 m-4">

	<h2 class="font-bold">Linked Discord Servers</h2>

	<div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
		@foreach(Auth()->user()->discordServers()->orderBy('name', 'asc')->get() as $server)
		<livewire:server-card :server="$server" :share_library="$server['share_library']" />
		@endforeach
	</div>

	</details>

</div>

</div>

@endsection