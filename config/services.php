<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],   

	'discord' => [
		'client_id' => env('DISCORD_CLIENT_ID'),
		'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'bot_token' => env('DISCORD_BOT_TOKEN'),
        'stat_servers_id' => env('STAT_SERVERS'),
        'stat_users_id' => env('STAT_USERS'),
        'stat_games_id' => env('STAT_GAMES'),
        'webhook_error' => env('WEBHOOK_DISCORD_ERROR'),
        'webhook_log' => env('WEBHOOK_DISCORD_LOG'),
        'webhook_sync' => env('WEBHOOK_DISCORD_SYNC'),
	],

	'steam_api_key' => env('STEAM_API_KEY'),

];
