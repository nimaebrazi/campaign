<?php

namespace App\Service\Voucher\Jobs;

use App\Service\Voucher\Logger\VoucherUsage\Contract\VoucherUsageLogger;
use App\Service\Voucher\Logger\VoucherUsage\VoucherUsageValueObject;
use App\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogVoucherUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Voucher
     */
    protected $voucher;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * Create a new job instance.
     *
     * @param $voucher
     * @param $phoneNumber
     */
    public function __construct($voucher, $phoneNumber)
    {
        $this->voucher = $voucher;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Execute the job.
     *
     * @param VoucherUsageLogger $voucherUsageLogger
     * @return void
     */
    public function handle(VoucherUsageLogger $voucherUsageLogger)
    {
        $voucherUsageModel = new VoucherUsageValueObject(
            $this->voucher, $this->phoneNumber
        );

        $voucherUsageLogger->log($voucherUsageModel);
    }

}
