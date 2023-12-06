<?php

namespace App\Http\Controllers;

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

        $user = User::where('discord_id', $user_id)->first();
        if (!$user) return json_encode(["message" => "User not found"]);

        $user->syncDiscordServers();
        $user->syncGames();

        $server = DiscordServer::where('discord_id', $server_id)->first();
        if (!$server) return json_encode(["message" => "Server not found"]);

        $discordServerUser = $user->discordServers()->where('server_id', $server->id)->first();
        if (!$discordServerUser) return json_encode(["message" => "Server User not Registered"]);

        $isLinked = $user->linkedAccounts->steam_id ? true : false;

        $discordServerUser->share_library = $state;
        $discordServerUser->save();

        return json_encode(["message" => "Sharing Changed", "state" => $state, "isLinked" => $isLinked]);
    }
}
