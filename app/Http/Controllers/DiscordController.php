<?php

namespace App\Http\Controllers;

/**
 * This controller handles interactions with the Discord API for the application.
 * It provides functions to rename Discord channels, set various statistics (servers, users, games) 
 * as channel names, and send messages to specified Discord channels using webhooks.
*/


class DiscordController extends Controller
{
    private static function renameChannel($channelId, $newName)
    {
        // Replace 'your_bot_token' with your actual bot token
        $botToken = config('services.discord')['bot_token'];

        // Discord API endpoint for updating a channel
        $endpoint = "https://discord.com/api/v10/channels/{$channelId}";

        // Data to be sent in the request body
        $data = [
            'name' => $newName
        ];

        // Headers for the request
        $headers = [
            'Authorization: Bot ' . $botToken,
            'Content-Type: application/json',
        ];

        // Initialize cURL session
        $ch = curl_init($endpoint);

        // Set cURL options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch) . "\n";
        }

        // Close cURL session
        curl_close($ch);
    }

    public static function setStat($channel, $value)
    {
        switch ($channel) {
            case 'servers':
                DiscordController::setServerStat($value);
                break;
            case 'users':
                DiscordController::setUserStat($value);
                break;
            case 'games':
                DiscordController::setGameStat($value);
                break;
        }
    }

    public static function setServerStat($value)
    {

        if (!$value) return;

        $channelId = config('services.discord')["stat_servers_id"];
        if (!$value) return;


        $text = "Connected Servers: $value";

        DiscordController::renameChannel($channelId, $text);
    }

    public static function setUserStat($value)
    {

        if (!$value) return;

        $channelId = config('services.discord')["stat_users_id"];
        if (!$channelId) return;

        $text = "Registered Users: $value";

        DiscordController::renameChannel($channelId, $text);
    }

    public static function setGameStat($value)
    {

        if (!$value) return;

        $channelId = config('services.discord')["stat_games_id"];
        if (!$channelId) return;

        $text = "Registered Games: $value";

        DiscordController::renameChannel($channelId, $text);
    }

    public static function sendMessage($channel, $message)
    {
        if (!$channel) {
            $channel = 'error';
            $newMessage = "A log request was sent but a channel was not specified \n **message:** $message";
            $message = $newMessage;
        }
        if (!$message) {
            $message = "A log request was sent but a message was not specified \n **Channel:** $channel";
            $channel = 'error';
        }

        $webhook_url = '';
        $username= '';

        switch ($channel) {
            case 'error':
                $webhook_url = config('services.discord')["webhook_error"];
                $username = 'Server Error';
                break;
            case 'log':
                $webhook_url = config('services.discord')["webhook_log"];
                $username = "Server Log";
                break;
            case 'sync':
                $webhook_url = config('services.discord')["webhook_sync"];
                $username = "Server Sync";
                break;
        }

        // Message content
        $message = [
            'content' => $message,
            'username' => $username,
        ];

        // Convert the message to JSON
        $jsonPayload = json_encode($message);

        // Set up the cURL request
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            error_log("Error while sending message to discord server channel $channel: " . curl_error($ch));
        }
    }
}
