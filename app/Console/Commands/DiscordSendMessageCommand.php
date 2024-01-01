<?php

namespace App\Console\Commands;

use App\Http\Controllers\DiscordController;
use Illuminate\Console\Command;

class DiscordSendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:send-message {channel} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the values of the 'channel' and 'message' parameters
        $channel = $this->argument('channel');
        $message = $this->argument('message');

        // Call the sendMessage function from DiscordController
        DiscordController::sendMessage($channel, $message);

        $this->info("Message sent to channel '$channel': $message");
    }
}
