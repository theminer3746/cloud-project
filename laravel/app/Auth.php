<?php

namespace App;

use App\Exceptions\UserException;
use Illuminate\Support\Facades\Hash;

class Auth
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function isLoggedIn()
    {
        return session()->get('auth.logged_in');
    }

    public function authenticate(string $username, string $password)
    {
        if (!$this->usernameExists($username)) {
            throw new UserException(null, UserException::USER_NOT_EXISTS);
        }
    
        $user = $this->user->where('username', $username)->first();

        if (!Hash::check($password, $user->password)) {
            throw new UserException(null, UserException::INCORRECT_PASSWORD);
        }

        return $user->id;
    }

    public function usernameExists(string $username)
    {
        return $this->user->where('username', $username)->exists();
    }
}
