<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\UserException;
use App\Auth;

class AuthController extends Controller
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function showLoginPage()
    {
        return view('login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $userId = $this->auth->authenticate($request->username, $request->password);
        } catch (UserException $e) {
            switch ($e->getCode()) {
                case UserException::INCORRECT_PASSWORD:
                    return redirect()->back()->withErrors([
                        'message' => 'Incorrect Password or user does not exists'
                    ]);

                    break;
                case UserException::USER_NOT_EXISTS:
                    return redirect()->back()->withErrors([
                        'message' => 'Incorrect Password or user does not exists'
                    ]);
                    
                    break;
            }
        }

        $request->session()->put('auth.logged_in', true);
        $request->session()->put('auth.username', $request->username);
        $request->session()->put('auth.user_id', $userId);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return response()->json([], 200);
    }
}
