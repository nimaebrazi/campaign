<?php


namespace App\Infrastructure\Repository\Contract;



interface VoucherUsageLogRepository
{
    /**
     * @param $code
     * @param $phoneNumber
     * @return boolean
     */
    public function exists($code, $phoneNumber);
}