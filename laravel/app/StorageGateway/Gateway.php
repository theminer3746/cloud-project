<?php

namespace App\StorageGateway;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\GatewayException;

class Gateway extends Model
{
    private $realGateway;

    protected $fillable = [
        'user_id',
        'activation_key',
        'arn',
        'name',
        'real_name',
        'region',
        'timezone',
        'type',
        'volume_arn',
        's3_bucket_name',
    ];

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

    public function activateGateway(
        string $gatewayActivationKey,
        string $gatewayName,
        int $customerId,
        string $password
    ) {
        if($this->where('activation_key', $gatewayActivationKey)->exists()){
            throw new GatewayException('Gateway already exists');
        }

        $result = $this->realGateway->activateAndInitializeGateway($gatewayActivationKey, $customerId, $password);
    
        $this->user_id = $customerId;
        $this->activation_key = $gatewayActivationKey;
        $this->arn = $result['GatewayARN'];
        $this->s3_bucket_name = $result['realBucketName'];
        $this->name = $gatewayName;
        $this->real_name = $result['realGatewayName'];
        $this->type = 'FILE_S3';

        $this->save();
    }
}
