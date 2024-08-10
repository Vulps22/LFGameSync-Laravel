<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DiscordController;

class UpdateComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the version log and sends a notification to Discord';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Determine the log file path
        $filePath = storage_path('logs/update.log');

        // Read the existing version from the log file, if it exists
        $currentVersion = null;
        if (file_exists($filePath)) {
            $currentVersion = file_get_contents($filePath);
        }

        // Generate the new version
        $year = date('Y');
        $month = date('m');
        $iteration = 1;

        if ($currentVersion) {
            $parts = explode('.', $currentVersion);
            if (count($parts) == 3 && $parts[0] == $year && $parts[1] == $month) {
                $iteration = (int)$parts[2] + 1;
            }
        }

        $newVersion = "{$year}.{$month}.{$iteration}";

        // Update or create the log file with the new version
        file_put_contents($filePath, $newVersion);

        // Notify via Discord
        DiscordController::sendMessage('error', "Updated to {$newVersion}");

        // Output success message to the console
        $this->info("Updated to version {$newVersion}");
    }
}
