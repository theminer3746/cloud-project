<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\StorageGateway\RealGateway;

class SetSMBGuestPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $gatewayARN;
    private $smbPassword;

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
    public function __construct(string $gatewayARN, string $smbPassword)
    {
        $this->gatewayARN = $gatewayARN;
        $this->smbPassword = $smbPassword;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RealGateway $realGateway)
    {
        $realGateway->setSMBGuestPassword($this->gatewayARN, $this->smbPassword);
    }
}
