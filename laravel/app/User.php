<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\UserException;
use Hash;

class User extends Model
{
    protected $fillable = [
        'username',
        'password'
    ];

    public function addUser(string $username, string $password)
    {
        if($this->usernameExists($username)){
            throw new UserException('Username already exists');
        }

        $this->username = $username;
        $this->password = Hash::make($password);

        $this->save();
    }

    public function usernameExists(string $username)
    {
        return $this->where('username', $username)->exists();
    }

    public function gateways()
    {
        return $this->hasMany('App\StorageGateway\Gateway');
    }
}
