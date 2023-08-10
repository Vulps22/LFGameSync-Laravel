<div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
	<div class="flex flex-col items-center pb-10 pt-5">

		<img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="https://cdn.discordapp.com/icons/{{$server['server_id']}}/{{$server['icon_hash']}}" alt="Image of Your Discord Avatar" />
		<h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $server['name'] }}</h5>

		<div class="flex mt-4 space-x-3 md:mt-6">
			<label class="relative inline-flex items-center cursor-pointer">
				<input type="checkbox" class="peer hidden" wire:model="share_library" class="sr-only peer">
				<div wire:click="toggleSharing" class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
			</label>
		</div>
	</div>
</div>