<?php


namespace App\Service\Voucher\Jobs;


use App\Service\Voucher\Logger\VoucherUsage\VoucherUsageValueObject;

class JobDispatcher
{
    /**
     * @param VoucherUsageValueObject $voucherUsageModel
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function loserJob($voucherUsageModel)
    {
        return LogVoucherUsageJob::dispatch(
            $voucherUsageModel->getVoucher(),
            $voucherUsageModel->getPhoneNumber()
        )->onQueue('voucher-loser-queue');
    }

    /**
     * @param VoucherUsageValueObject $voucherUsageModel
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function winnerJob($voucherUsageModel)
    {
        return LogVoucherUsageJob::dispatch(
            $voucherUsageModel->getVoucher(),
            $voucherUsageModel->getPhoneNumber()
        )->onQueue('voucher-winner-queue');

    }

}