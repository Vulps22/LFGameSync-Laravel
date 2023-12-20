<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LinkToken;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AccountLinking extends Controller
{

    public $token;

    public function __construct()
    {
        if (Auth::check()) return;
       // dd(Auth::user());
        $token = request()->get('token');
        if (!$token) {
            echo "Token Not Provided";
            return;
        }
        $this->token = LinkToken::where('token', $token)->first();
        Auth::loginUsingId($this->token->user_id);
        if (!Auth::check()) {
            echo "Authentication Failed, Please Try again or open a ticket on the Support Server";
            exit();
        }
        Auth::user()->isTokenLogin = false;
    }

    public function index(): View
    {

        return view('account-linking', [
            'token' => $this->token
        ]);
    }
}
