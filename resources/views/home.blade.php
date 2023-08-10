<!-- resources/views/home.blade.php -->

@extends('layouts.app')

@section('content')

<header class="bg-gray-900 text-white px-4 py-32">
	<div class="max-w-3xl mx-auto text-center">
		<h1 class="text-3xl font-bold">Meet Gamers. Make Friends.</h1>
		<p class="text-xl mt-4">Connect your gaming profiles and find new squadmates today!</p>
		<button class="bg-white text-indigo-500 px-6 py-3 rounded-full mt-8" onclick="window.location.href='/login/discord'">
			<i class="fa-brands fa-discord"></i>
			Sign In with Discord
		</button>	</div>
</header>

<section id="features" class="bg-gray-800 py-20">
	<div class="max-w-6xl mx-auto">
		<h2 class="text-3xl text-white font-bold mb-16 text-center">Key Features</h2>

		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

			<!-- Feature cards... -->
			<div class="flex items-center">
				<div class="mr-4">
					<i class="fa-solid fa-users text-5xl text-indigo-500"></i>
				</div>
				<div>
					<h3 class="text-gray-300 text-xl font-bold mb-2">Find Gamers</h3>
					<p class="text-gray-300">Find gamers who play the same games as you and want to play together!</p>
				</div>
			</div>
			<div class="flex items-center">
				<div class="mr-4">
					<i class="fa-solid fa-book text-5xl text-indigo-500"></i>
				</div>
				<div>
					<h3 class="text-gray-300 text-xl font-bold mb-2">Share Your Library</h3>
					<p class="text-gray-300">Share your Game Library with whole discord communities!</p>
				</div>
			</div>
			<div class="flex items-center">
				<div class="mr-4">
					<i class="fa-solid fa-users text-5xl text-indigo-500"></i>
				</div>
				<div>
					<h3 class="text-gray-300 text-xl font-bold mb-2">Play Together</h3>
					<p class="text-gray-300">Play the games you love, together!</p>
				</div>
			</div>
		</div>
</section>

<section id="stats" class="bg-gray-950 py-20">
	<div class="max-w-6xl mx-auto">
		<h2 class="text-3xl text-white font-bold mb-16 text-center">Connecting Players Worldwide!</h2>

		<div class="grid grid-cols-1 md:grid-cols-2">

			<!-- Feature cards... -->
			<div class="flex items-center lg:pl-40">
				<div class="mr-4">
					<i class="fa-solid fa-users text-5xl text-indigo-500"></i>
				</div>
				<div>
					<h3 class="text-gray-300 text-xl font-bold mb-2">Registered Gamers</h3>
					<p class="text-gray-300">{{$playerCount}} gamers sharing their libraries</p>
				</div>
			</div>
			<div class="flex items-center lg:pl-40">
				<div class="mr-4">
					<i class="fa-solid fa-book text-5xl text-indigo-500"></i>
				</div>
				<div>
					<h3 class="text-gray-300 text-xl font-bold mb-2">Discord Servers</h3>
					<p class="text-gray-300">{{$discordCount}} servers connecting players</p>
				</div>
			</div>
		</div>
</section>

<section class="bg-indigo-500 py-16">
	<div class="max-w-3xl mx-auto text-center">
		<h3 class="text-3xl text-white font-bold">Link your accounts now!</h3>
		<button class="bg-white text-indigo-500 px-6 py-3 rounded-full mt-8" onclick="window.location.href='/login/discord'">
			<i class="fa-brands fa-discord"></i>
			Sign In with Discord
		</button>
	</div>
</section>

@endsection