<?php


namespace App\Infrastructure\Repository\Cache;

use App\Infrastructure\Repository\Contract\VoucherUsageLogRepository as VoucherUsageLogRepositoryIntreface;
use Illuminate\Contracts\Redis\Factory as RedisFactory;


class VoucherUsageLogRepository implements VoucherUsageLogRepositoryIntreface
{
    /**
     * @var RedisFactory
     */
    protected $redis;

    /**
     * VoucherUsageLogRepository constructor.
     * @param RedisFactory $redis
     */
    public function __construct(RedisFactory $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param $code
     * @param $phoneNumber
     * @return boolean
     */
    public function exists($code, $phoneNumber)
    {
        $codes = $this->redis->lrange($phoneNumber, 0, -1);

        if (in_array($code, $codes)) {
            return true;
        }

        return false;
    }
}