<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function gateway()
    {
        return $this->hasMany('App\StorageGateway\Gateway');
    }
}
