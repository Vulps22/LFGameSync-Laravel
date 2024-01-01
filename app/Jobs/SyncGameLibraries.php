<?php

namespace App\Jobs;

use App\Http\Controllers\DiscordController;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncGameLibraries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */

    public function handle()
    {
        try {
            DiscordController::sendMessage('sync', "Starting Hourly Sync Job");

            // Get users to sync
            $users = User::needsSyncing()->get();
            $userCount = count($users);
            DiscordController::sendMessage('sync', "Syncing $userCount Users");

            // Sync each user
            foreach ($users as $user) {
                $user->syncGames();
            }

            DiscordController::sendMessage('sync', "Successfully Synced $userCount users");
        } catch (Exception $e) {
            DiscordController::sendMessage('error', "Error while running Hourly Sync: \n {{$e->getMessage()}}");
            DiscordController::sendMessage('sync', "Hourly Sync Failed! An Exception was caught");
        }
    }
}
