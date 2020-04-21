<?php

namespace App\StorageGateway;

use App\Exceptions\GatewayException;
use AWS;
use App\S3;
use App\StorageGateway\Gateway;
use App\Jobs\AddAllAvailableLocalDisksToCache;
use App\Jobs\SetSMBGuestPassword;
use App\Jobs\CreateSMBFileShare;
use Aws\StorageGateway\Exception\StorageGatewayException;

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

        AddAllAvailableLocalDisksToCache::withChain([
            new SetSMBGuestPassword($gatewayARN, $smbPassword),
            new CreateSMBFileShare($gatewayARN, $bucketARN, $customerId),
        ])->dispatch($gatewayARN)->delay(now()->addSeconds(30));

        return $activationResult;
    }

    public function activateGateway(string $gatewayActivationKey, int $customerId)
    {
        $s3CreationResult = $this->s3->createBucketAndBlockPublicAccess('gateway-customer-' . $customerId);

        $realGatewayName = 'gateway-customer-' . $customerId . '-' . bin2hex(random_bytes(10));

        $gatewayActivationResult = $this->gatewayClient->activateGateway([
            'ActivationKey' => $gatewayActivationKey,
            'GatewayName' => $realGatewayName,
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
                    'Value' => strval($customerId),
                ],
            ],
        ]);

        return [
            'GatewayARN' => $gatewayActivationResult['GatewayARN'],
            'realBucketName' => $s3CreationResult['realBucketName'],
            'bucketLocation' => $s3CreationResult['location'],
            'bucketARN' => $s3CreationResult['bucketARN'],
            'realGatewayName' => $realGatewayName,
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
        $smbShareCreationResult = $this->gatewayClient->createSMBFileShare([
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

        return [
            'FileShareARN' => $smbShareCreationResult['FileShareARN'],
        ];
    }

    public function getSMBAccessUrl(string $gatewayARN)
    {
        $ipv4 = null;

        try {
            $ipv4 = $this->gatewayClient->describeGatewayInformation([
                'GatewayARN' => $gatewayARN
            ])['GatewayNetworkInterfaces'][0]['Ipv4Address'];
        } catch (StorageGatewayException $e) {

        }

        $s3BucketName = (new Gateway)->where('arn', $gatewayARN)->value('s3_bucket_name');

        if(is_null($ipv4)){
            return 'Gateway is offline';
        } else {
            return '\\\\' . $ipv4 . '\\' . $s3BucketName;
        }
    }
}
