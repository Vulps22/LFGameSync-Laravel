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
        if (!$server_id) return "Server ID not provided";

        $user_id = $request->input('user_id');
        if (!$user_id) return "No User ID Provided";

        $state = $request->input('state');
        if (!($state === "true" || $state === "false") && !is_bool($state)) return "State must be a Boolean";
        
        //if state is not a boolean, make it a boolean
        if (!is_bool($state)) {
            if ($state === "true") $state = true;
            else $state = false;
        }

        $user = User::where('discord_id', $user_id)->first();
        if (!$user) return "User not found";

        $user->syncDiscordServers();

        $server = DiscordServer::where('discord_id', $server_id)->first();
        if (!$server) return "Server not found";

        $discordServerUser = $user->discordServers()->where('server_id', $server->id)->first();
        if (!$discordServerUser) return "Server User not Registered";

        $discordServerUser->share_library = $state;
        $discordServerUser->save();

        return "Sharing Set to $state";
    }
}
