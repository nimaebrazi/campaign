<?php


namespace App\Service\Voucher\Exception;


use Exception;

/**
 * Class VoucherException
 * @author Nima Ebrazi <nima.ebrazi@gmail.com>
 */
class VoucherException extends Exception
{
    /**
     * @return VoucherCodeNotExistsException
     */
    public static function voucherCodeNotExists()
    {
        return new VoucherCodeNotExistsException();
    }

    /**
     * @return ExceededVoucherCodeUsageException
     */
    public static function exceededVoucherCodeUsage()
    {
        return new ExceededVoucherCodeUsageException();
    }

    /**
     * @return FlagServiceException
     */
    public static function flagServiceException()
    {
        return new FlagServiceException();
    }

    /**
     * @return UsedBeforeException
     */
    public static function usedBeforeException()
    {
        return new UsedBeforeException();
    }
}