<?php

namespace App\Jobs;

use App\Http\Controllers\DiscordController;
use App\Models\User;
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
        DiscordController::sendMessage('sync', "Starting Hourly Sync Job");
        // Get users to sync
        $users = User::needsSyncing()->get();

        DiscordController::sendMessage('sync', "Syncing {{count($users)}} Users");
        
        // Sync each user
        foreach ($users as $user) {
            $user->syncGames();
        }

        DiscordController::sendMessage('sync', "Successfully Synced {{count($users)}} users");
    }
}
