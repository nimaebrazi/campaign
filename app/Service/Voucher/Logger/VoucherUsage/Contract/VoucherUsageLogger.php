<?php


namespace App\Service\Voucher\Logger\VoucherUsage\Contract;


use App\Service\Voucher\Logger\VoucherUsage\VoucherUsageValueObject;

interface VoucherUsageLogger
{
    /**
     * Log usage for specific ID.
     *
     * @param VoucherUsageValueObject $voucherUsageModel
     * @return mixed
     */
    public function log(VoucherUsageValueObject $voucherUsageModel);
}