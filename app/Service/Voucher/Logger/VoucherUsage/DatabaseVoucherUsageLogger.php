<?php


namespace App\Service\Voucher\Logger\VoucherUsage;


use App\Service\Voucher\Logger\VoucherUsage\Contract\VoucherUsageLogger;
use App\VoucherUsageLog;

class DatabaseVoucherUsageLogger implements VoucherUsageLogger
{

    /**
     * Log usage for specific ID.
     *
     * @param VoucherUsageValueObject $voucherUsageModel
     * @return mixed
     */
    public function log(VoucherUsageValueObject $voucherUsageModel)
    {
        $voucherUsageLog = new VoucherUsageLog();

        $voucherUsageLog->fill([
            'phone_number' => $voucherUsageModel->getPhoneNumber(),
            'voucher_id'   => $voucherUsageModel->getVoucher()->id,
            'voucher_code' => $voucherUsageModel->getVoucher()->code,
            'is_winner'    => $voucherUsageModel->isWinner()
        ]);

        return $voucherUsageLog->save();
    }
}