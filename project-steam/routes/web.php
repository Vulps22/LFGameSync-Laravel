<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SteamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\DiscordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/link/steam', [SteamController::class, 'redirectToSteam'])->name('steam.login');
Route::get('/link/steam/callback', [SteamController::class, 'handleSteamCallback'])->name('steam.callback');

// routes/web.php

Route::get('/login/discord', [DiscordController::class, 'redirectToDiscord']);
Route::get('/login/discord/callback', [DiscordController::class, 'handleDiscordCallback'])->name('discord.callback');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');