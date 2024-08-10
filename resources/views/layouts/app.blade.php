<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>LFGameSync</title>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	@livewireStyles
	@vite('resources/css/app.css')
	<style>
		.cookie-popup {
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			background-color: #1f2937;
			padding: 1rem;
			display: none;
		}

		.cookie-show {
			display: block;
		}
	</style>
</head>

<body class="antialiased bg-gray-900 text-white flex flex-col min-h-screen">
	<div class="flex-grow">
		@yield('content')
	</div>
	<footer class="py-4 text-center bg-gray-900 text-white">
		<div class="flex justify-center items-center h-full">
			<p class="mr-4">&copy; 2023-{{ date('Y') }} LFGameSync</p>
			<a href="{{ route('privacy') }}" class="mr-4">Privacy Policy</a>
			<a href="{{ route('terms') }}" class="mr-4">Privacy Policy</a>
			@if(!isset($_COOKIE['accept_cookies']))
			<div class="cookie-popup bg-gray-800 rounded-md p-4">
				<p class="mb-2">We use cookies for essential functions only. By using our site, you consent to our use of cookies.</p>
				<button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md" onclick="acceptCookies()">Accept</button>
			</div>
			@endif
		</div>
	</footer>
	@livewireScripts
	<script>
		function acceptCookies() {
			console.log('accepting cookies');
			document.cookie = "accept_cookies=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
			document.querySelector('.cookie-popup').classList.remove('cookie-show');
		}

		if (!document.cookie.includes('accept_cookies=true')) {
			document.querySelector('.cookie-popup').classList.add('cookie-show');
		}

		// Set footer to bottom of view if content is less than view height
		const body = document.querySelector('body');
		const html = document.querySelector('html');
		const footer = document.querySelector('footer');
		const windowHeight = window.innerHeight;
		const bodyHeight = body.offsetHeight;
		const htmlHeight = html.offsetHeight;
		const footerHeight = footer.offsetHeight;

		if (bodyHeight + footerHeight < windowHeight || htmlHeight + footerHeight < windowHeight) {
			footer.style.position = 'fixed';
			footer.style.bottom = '0';
			footer.style.left = '0';
			footer.style.right = '0';
		}
	</script>
</body>

</html>