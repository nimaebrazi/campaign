<?php


namespace App\Service\Voucher\Logger\VoucherUsage;


use App\Service\Voucher\Logger\VoucherUsage\Contract\VoucherUsageLogger;
use Illuminate\Contracts\Redis\Factory;

class RedisVoucherUsageLogger implements VoucherUsageLogger
{

    /**
     * @var Factory
     */
    protected $redis;

    /**
     * RedisVoucherUsageLogger constructor.
     * @param Factory $redis
     */
    public function __construct(Factory $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Log usage for specific ID.
     *
     * @param VoucherUsageValueObject $voucherUsageModel
     * @return mixed
     */
    public function log(VoucherUsageValueObject $voucherUsageModel)
    {
        return $this->redis->rpush(
            $voucherUsageModel->getPhoneNumber(),
            $voucherUsageModel->getVoucher()->code
        );
    }
}