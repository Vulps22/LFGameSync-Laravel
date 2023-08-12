<div class="flex flex-col w-full border border-gray-700 rounded-lg">
	<div class="mt-5 flex flex-col items-center">
		<div class="flex-shrink-0">
			<img src="{{ asset('img/steam_logo.png') }}" class="w-12 h-12 rounded-full">
		</div>

		<div>
			<h1 class="font-medium">Not Linked</h1>
			<p class="w-full text-center text-sm text-gray-400">{{$type}}</p>
		</div>
	</div>

	<div class="mx-5 mb-5 flex flex-col">
    <a href="/link/steam" class="mx-auto h-9 text-white px-4 py-2 rounded mt-4">
        <img src="{{ asset('img/steam_01.png') }}" alt="Sign in through Steam" style="max-width: 100%; height: auto;">
    </a>
</div>
</div>