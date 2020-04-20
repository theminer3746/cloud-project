<?php

namespace App\StorageGateway;

use App\Exceptions\GatewayException;
use AWS;
use App\S3;
use App\StorageGateway\Gateway;

class RealGateway
{
    private $gatewayClient;
    private $s3;

    public function __construct(S3 $s3)
    {
        $this->gatewayClient = AWS::createClient('storagegateway');
        $this->s3 = $s3; 
    }

    public function activateAndInitializeGateway(string $gatewayActivationKey, int $customerId, string $smbPassword)
    {
        $activationResult = $this->activateGateway($gatewayActivationKey, $customerId);

        $gatewayARN = $activationResult['GatewayARN'];
        $bucketARN = $activationResult['bucketARN'];

        $this->addAllAvailableLocalDisksToCache($gatewayARN);

        $this->setSMBGuestPassword($gatewayARN, $smbPassword);

        $this->createSMBFileShare($gatewayARN, $bucketARN, $customerId);
    }

    public function activateGateway(string $gatewayActivationKey, int $customerId)
    {
        $s3CreationResult = $this->s3->createBucketAndBlockPublicAccess('gateway-customer-' . $customerId);

        $gatewayActivationResult = $this->gatewayClient->activateGateway([
            'ActivationKey' => $gatewayActivationKey,
            'GatewayName' => 'gateway-customer-' . $customerId,
            'GatewayRegion' => 'ap-southeast-1',
            'GatewayTimezone' => 'GMT+7:00',
            'GatewayType' => 'FILE_S3',
            'Tags' => [
                [
                    'Key' => 'activity',
                    'Value' => 'project',
                ],
                [
                    'Key' => 'customer_id',
                    'Value' => (string) $customerId,
                ],
            ],
        ]);

        return [
            'GatewayARN' => $gatewayActivationResult['GatewayARN'],
            'realBucketName' => $s3CreationResult['realBucketName'],
            'bucketLocation' => $s3CreationResult['location'],
            'bucketARN' => $s3CreationResult['bucketARN'],
        ];
    }

    public function listLocalDisks(string $gatewayARN)
    {
        return $this->gatewayClient->listLocalDisks([
            'GatewayARN' => $gatewayARN
        ])['Disks'];
    }

    public function getAvailableLocalDisksId(string $gatewayARN)
    {
        $diskIdArray = [];

        foreach($this->listLocalDisks($gatewayARN) as $disk){
            if ($disk['DiskAllocationType'] == 'AVAILABLE' && $disk['DiskStatus'] == 'present') {
                $diskIdArray[] = $disk['DiskId'];
            }
        }

        return $diskIdArray;
    }

    public function addAllAvailableLocalDisksToCache(string $gatewayARN)
    {
        $diskIdArray = $this->getAvailableLocalDisksId($gatewayARN);

        $this->gatewayClient->addCache([
            'DiskIds' => $diskIdArray,
            'GatewayARN' => $gatewayARN,
        ]);
    }

    public function setSMBGuestPassword(string $gatewayARN, string $smbPassword)
    {
        $this->gatewayClient->setSMBGuestPassword([
            'GatewayARN' => $gatewayARN, // REQUIRED
            'Password' => $smbPassword, // REQUIRED
        ]);
    }

    public function createSMBFileShare(string $gatewayARN, string $bucketARN, int $customerId)
    {
        $this->gatewayClient->createSMBFileShare([
            'Authentication' => 'GuestAccess',
            'ClientToken' => sha1(random_bytes(40)), // REQUIRED
            'DefaultStorageClass' => 'S3_STANDARD',
            'GatewayARN' => $gatewayARN, // REQUIRED
            'LocationARN' => $bucketARN, // REQUIRED
            'ObjectACL' => 'private',
            'ReadOnly' => false,
            'Role' => 'arn:aws:iam::719320520338:role/Allow-cloud-project-account-to-policy', // REQUIRED
            'Tags' => [
                [
                    'Key' => 'activity', // REQUIRED
                    'Value' => 'project', // REQUIRED
                ],
                [
                    'Key' => 'customer_id', // REQUIRED
                    'Value' => (string) $customerId, // REQUIRED
                ],
            ],
        ]);
    }
}
