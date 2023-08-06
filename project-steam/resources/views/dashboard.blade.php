<!-- resources/views/dashboard.blade.php-->

@extends('layouts.app')

@section('content')
  <h1>Dashboard</h1>
  <p>Welcome {{ Auth::user()->discordUser()['username'] }}!</p>
  <!--image will be resources/img/steam_01.png-->
  @if(!Auth::user()->steam_id)
    <p>Link your Steam account.</p>
  <img src="{{ asset('img/steam_01.png') }}" alt="steam logo" onclick="window.location.href='/link/steam'">
  @else
    <p>Linked to Steam Account: {{Auth::user()->steamUser()['personaname']}}</p>
  @endif
@endsection