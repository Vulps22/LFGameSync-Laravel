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
            return redirect('/');
        }

        dump($token);
        $this->token = LinkToken::where('token', $token)->first();
        dump($this->token);

        if (!$this->token) dd("TOKEN NOT FOUND!");

        Auth::loginUsingId($this->token->user_id);
        
        if (!Auth::check()) {
            echo "Authentication Failed, Please Try again or open a ticket on the Support Server";
            exit();
        }

        Cookie::queue(Cookie::make('oneTimeToken', $this->token->token, 15));
        Auth::user()->isTokenLogin = false;
    }

    public function index(Request $request): View
    {
        return view('account-linking', [
            'token' => $this->token
        ]);
    }
}
