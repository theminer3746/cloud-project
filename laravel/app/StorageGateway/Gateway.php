<?php

namespace App\StorageGateway;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    private $realGateway;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function setRealGateway(RealGateway $realGateway)
    {
        $this->realGateway = $realGateway;
    }

    public function getRealGateway()
    {
        return $this->realGateway;
    }

    public function activateGateway(string $gatewayActivationKey, string $gatewayName, int $customerId)
    {
        if($this->where('activation_key', $gatewayActivationKey)->exists()){
            throw new GatewayException('Gateway already exists');
        }

        $this->realGateway->activateGateway($gatewayActivationKey, $gatewayName, $customerId);
    }
}
