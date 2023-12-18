<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "Api" middleware group. Make something great!
|
*/

Route::get('/lfg/find', [App\Http\Controllers\Api\v1\LFGController::class, 'find'])->name('lfg.find');
Route::get('/lfg/find_game', [\App\Http\Controllers\Api\v1\LFGController::class, 'find_game'])->name('lfg.find_game');
Route::get('/lfg/get_game', [\App\Http\Controllers\Api\v1\LFGController::class, 'get_game'])->name('lfg.get_game');

Route::post('/lfg/register_server', [App\Http\Controllers\Api\v1\LFGController::class, 'register_server'])->name('lfg.register_server');
Route::post('/lfg/remove_server', [App\Http\Controllers\Api\v1\LFGController::class, 'remove_server'])->name('lfg.remove_server');

Route::post('/server/set_sharing', [App\Http\Controllers\Api\V1\ServerController::class, 'set_sharing'])->name('server.set_sharing');
Route::post('/server/register_user', [\App\Http\Controllers\Api\V1\ServerController::class, 'register_user'])->name('server.register_user');


Route::post ('/account/link_steam', [App\Http\Controllers\Api\V1\AccountsController::class, 'link_steam'])->name('account.link_steam');