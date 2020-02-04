<?php


namespace App\Service\Voucher\ValueObject;


/**
 * Class VoucherValueObject
 * @author Nima Ebrazi <nima.ebrazi@gmail.com>
 */
class VoucherValueObject
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @return static
     */
    public static function make()
    {
        return new static;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return VoucherValueObject
     */
    public function code($code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return VoucherValueObject
     */
    public function phoneNumber($phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}