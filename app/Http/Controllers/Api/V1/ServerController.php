<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DiscordServer;
use App\Models\User;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function set_sharing(Request $request)
    {
        $server_id = $request->input('server_id');
        if (!$server_id) return json_encode(["message" => "Server ID not provided"]);

        $user_id = $request->input('user_id');
        if (!$user_id) return json_encode(["message" => "No User ID Provided"]);

        $state = $request->input('state');
        if (!($state === "true" || $state === "false") && !is_bool($state)) return "State must be a Boolean";

        //if state is not a boolean, make it a boolean
        if (!is_bool($state)) {
            if ($state === "true") $state = true;
            else $state = false;
        }

        $user = User::where(['discord_id' => $user_id])->first();
        if (!$user) return json_encode(['message' => 'User not Found']);

        $user->syncDiscordServers();
        $user->syncGames();

        $server = DiscordServer::where('discord_id', $server_id)->first();
        if (!$server) return json_encode(["message" => "Server not found"]); //DO NOT try to register a new server from here. Always from the bot

        $discordServerUser = $user->discordServers()->where('server_id', $server->id)->firstOrNew();
        if (!$discordServerUser->exists) {
            $discordServerUser->server_id = $server->id;
            $discordServerUser->save();
        }


        $isLinked = $user->linkedAccounts->steam_id ? true : false;

        $discordServerUser->share_library = $state;
        $discordServerUser->save();

        return json_encode(["message" => "Sharing Changed", "state" => $state, "isLinked" => $isLinked]);
    }

    public function register_user(Request $request)
    {
        $server_id = $request->input('server_id');
        if (!$server_id) return json_encode(["message" => "Server ID not provided"]);

        $user_id = $request->input('user_id');
        if (!$user_id) return json_encode(["message" => "No User ID Provided"]);

        $user_name = $request->input('name');
        if (!$user_name) return json_encode(["User Name not provided"]);

        $user = User::firstOrNew(['discord_id' => $user_id]);
        if (!$user->exists) {
            $user->discord_name = $user_name;
            $user->save();
        }

        $server = DiscordServer::where(['discord_id' => $server_id])->first();
        if (!$server) return json_encode(["message" => "Server not Found"]); //DO NOT register new servers here. Always through the bot!

        $serverUser = $user->discordServers()->where('server_id', $server->id)->firstOrNew();
        if (!$serverUser->exists) {
            $serverUser->server_id = $server->id;
            $serverUser->save();
        }
        return json_encode(["message" => "User Registered"]);
    }
}
