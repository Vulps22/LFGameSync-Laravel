<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LinkToken;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
//use Cookie;
use Symfony\Component\HttpFoundation\Cookie;

class AccountLinking extends Controller
{

    public $token;

    public function __construct()
    {
        if (Auth::check()) return;
        $token = request()->get('token') ?? request()->cookie('oneTimeToken');
        dump($token);
        if (!$token) {
            return redirect('/');
        }
        $this->token = LinkToken::where('token', $token)->first();
        Auth::loginUsingId($this->token->user_id);
        if (!Auth::check()) {
            echo "Authentication Failed, Please Try again or open a ticket on the Support Server";
            exit();
        }
        Auth::user()->isTokenLogin = false;
        // Attach the cookie to the response

        $cookie = cookie('oneTimeToken', $token, 15);

        return response('Cookie')->cookie($cookie);

    }

    public function index(): View
    {
        return view('account-linking', [
            'token' => $this->token
        ]);
    }
}
