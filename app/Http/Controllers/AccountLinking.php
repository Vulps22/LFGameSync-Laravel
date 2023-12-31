<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LinkToken;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AccountLinking extends Controller
{

    public $token;

    public function __construct()
    {
        //if logged in, no need to do everything else
        if (Auth::check()) return;

        $token = request()->get('token') ?? Cookie::get('oneTimeToken');

        if (!$token) {
            abort(401);
        }

        $this->token = LinkToken::where('token', $token)->first();

        if (!$this->token) abort(401);

        //if token is expired, delete it
        if ($this->token->isExpired()) {
            $this->token->delete();
            abort(401);
        }

        Auth::loginUsingId($this->token->user_id);

        if (!Auth::check()) {
            abort(401);
        }

        Cookie::queue(Cookie::make('oneTimeToken', $token, 15));
        Auth::user()->isTokenLogin = false;
    }

    public function index(Request $request): View
    {
        return view('account-linking', [
            'token' => $this->token
        ]);
    }
}
