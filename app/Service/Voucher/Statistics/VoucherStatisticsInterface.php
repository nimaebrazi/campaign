<?php


namespace App\Service\Voucher\Statistics;


interface VoucherStatisticsInterface
{
    /**
     * Get usage of code count.
     *
     * @param string $key
     * @param string $code
     * @return mixed
     */
    public function count($key, $code);

    /**
     * Increment field.
     *
     * @param $key
     * @param $field
     * @param int $incr
     * @return mixed
     */
    public function increment($key, $field, $incr = 1);

}