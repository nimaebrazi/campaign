<?php


namespace App\Service\Voucher\Statistics;


use Illuminate\Contracts\Redis\Factory;

class RedisVoucherStatistics implements VoucherStatisticsInterface
{
    /**
     * @var Factory
     */
    protected $redis;


    /**
     * RedisVoucherStatistics constructor.
     * @param Factory $redis
     */
    public function __construct(Factory $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Get usage of code count.
     *
     * @param string $key
     * @param string $field
     * @return mixed
     */
    public function count($key, $field)
    {
        return $this->redis->hget($key, $field);
    }

    /**
     * Increment field.
     *
     * @param $key
     * @param $field
     * @param int $incr
     * @return mixed
     */
    public function increment($key, $field, $incr = 1)
    {
        return $this->redis->hincrby($key, $field, $incr);
    }


}