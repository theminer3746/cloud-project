<?php

namespace App;

use AWS;
use Aws\S3\Exception\S3Exception;

class S3
{
    private $s3;

    public function __construct()
    {
        $this->s3 = AWS::createClient('s3');
    }

    public function createBucketAndBlockPublicAccess(string $bucketName) : array
    {
        $createBucketResult  = $this->createBucket($bucketName);
        
        $this->putPublicAccessBlock($createBucketResult['realBucketName']);

        return $createBucketResult;
    }

    public function createBucket(string $bucketName) : array
    {
        $success = false;

        do {
            $realBucketName = $bucketName . '-' . bin2hex(random_bytes(10));

            try {
                $result = $this->s3->createBucket([
                    'ACL' => 'private',
                    'Bucket' => $realBucketName,
                    'CreateBucketConfiguration' => [
                        'LocationConstraint' => 'ap-southeast-1',
                    ],
                ]);

                $success = true;

                return [
                    'realBucketName' => $realBucketName,
                    'location' => $result['Location'],
                    'bucketARN' => 'arn:aws:s3:::' . $realBucketName,
                ];
            } catch (S3Exception $e) {

            }
        } while (!$success);
    }

    public function putPublicAccessBlock(string $bucketName)
    {
        $this->s3->putPublicAccessBlock([
            'Bucket' => $bucketName,
            'PublicAccessBlockConfiguration' => [
                'BlockPublicAcls' => true,
                'BlockPublicPolicy' => true,
                'IgnorePublicAcls' => true,
                'RestrictPublicBuckets' => true,
            ],
        ]);

        return true;
    }
}
