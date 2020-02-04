<?php


namespace App\Infrastructure\Repository\Eloquent;


use App\Infrastructure\Repository\Contract\VoucherUsageLogRepository as VoucherUsageLogRepositoryIntreface;
use App\VoucherUsageLog;

class VoucherUsageLogRepository implements VoucherUsageLogRepositoryIntreface
{
    /**
     * @param $code
     * @param $phoneNumber
     * @return boolean
     */
    public function exists($code, $phoneNumber)
    {
        $voucher = VoucherUsageLog::where('voucher_code', $code)
            ->where('phone_number', $phoneNumber)
            ->first();

        if (is_null($voucher)) {
            return false;
        }

        return true;
    }
}