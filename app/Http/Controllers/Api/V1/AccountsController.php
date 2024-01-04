<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LinkToken;
use App\Models\User;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function link_token(Request $request)
    {
        $discordId = $request->discord_id;

        if(!$discordId) return "Discord ID Missing";

        $user = User::firstOrCreate(['discord_id' => $discordId, 'discord_name' => 'Token']);
        if(!$user->exists()) $user->save();

        $token = hash('sha256', $discordId . now());

        $link = LinkToken::firstOrNew([
            'user_id' => $user->id
        ]);

        $link->token = $token;
        $link->expires = now()->addMinutes(15);
        $link->save();

        return response()->json([
            'token' => $token
        ]);
    }
}
