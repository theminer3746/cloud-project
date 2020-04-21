<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\StorageGateway\RealGateway;

class AddAllAvailableLocalDisksToCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $gatewayARN;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 15;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $gatewayARN)
    {
        $this->gatewayARN = $gatewayARN;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RealGateway $realGateway)
    {
        $realGateway->addAllAvailableLocalDisksToCache($this->gatewayARN);
    }
}
