<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\StorageGateway\RealGateway;
use App\StorageGateway\Gateway;

class CreateSMBFileShare implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $gatewayARN;
    private $bucketARN;
    private $customerId;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 10;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $gatewayARN, string $bucketARN, int $customerId)
    {
        $this->gatewayARN = $gatewayARN;
        $this->bucketARN = $bucketARN;
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RealGateway $realGateway, Gateway $gateway)
    {
        $result = $realGateway->createSMBFileShare($this->gatewayARN, $this->bucketARN, $this->customerId);
    
        $gateway->where('arn', $this->gatewayARN)->update([
            'volume_arn' => $result['FileShareARN'],
        ]);
    }
}
