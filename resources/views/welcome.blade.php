<!-- resources/views/home.blade.php -->

@extends('layouts.app')

@section('content')

<header class="bg-gray-900 text-white px-4 py-32">
  <div class="max-w-3xl mx-auto text-center">
    <h1 class="text-3xl font-bold">Meet Gamers. Make Friends.</h1>
    <p class="text-xl mt-4">Connect your gaming profiles and find new squadmates today!</p>
    <button class="bg-indigo-500 text-white px-6 py-3 rounded-full mt-8 hover:bg-indigo-400">Get Started</button>
  </div>
</header>

<section id="features" class="bg-gray-800 py-20">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl text-white font-bold mb-16 text-center">Key Features</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

      <!-- Feature cards... -->

    </div>
  </div>
</section>

<section class="bg-indigo-500 py-16">
  <div class="max-w-3xl mx-auto text-center">
    <h3 class="text-3xl text-white font-bold">Join the community now!</h3>
    <button class="bg-white text-indigo-500 px-6 py-3 rounded-full mt-8">Sign Up</button> 
  </div>  
</section>

@endsection