<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/lfg/find', [App\Http\Controllers\LFGController::class, 'find'])->name('lfg.find');
Route::get('/lfg/find_game', [\App\Http\Controllers\LFGController::class, 'find_game'])->name('lfg.find_game');
Route::get('/lfg/get_game', [\App\Http\Controllers\LFGController::class, 'get_game'])->name('lfg.get_game');

Route::post('/lfg/register_server', [App\Http\Controllers\LFGController::class, 'register_server'])->name('lfg.register_server');
Route::post('/lfg/remove_server', [App\Http\Controllers\LFGController::class, 'remove_server'])->name('lfg.remove_server');

Route::post('/server/set_sharing', [App\Http\Controllers\ServerController::class, 'set_sharing'])->name('server.set_sharing');
Route::post('/server/register_user', [\App\Http\Controllers\ServerController::class, 'register_user'])->name('server.register_user');
