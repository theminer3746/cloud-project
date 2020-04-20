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
                    return response()->json([
                        'message' => 'Incorrect Password or user does not exists'
                    ], 401);
                    break;
                case UserException::USER_NOT_EXISTS:
                    return response()->json([
                        'message' => 'Incorrect Password or user does not exists'
                    ], 401);
                    break;
            }
        }

        $request->session()->put('auth.logged_in', true);
        $request->session()->put('auth.username', $request->username);
        $request->session()->put('customer_id', $userId);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return response()->json([], 200);
    }
}
