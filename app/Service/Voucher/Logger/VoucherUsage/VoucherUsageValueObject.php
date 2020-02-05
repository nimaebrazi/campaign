<?php


namespace App\Service\Voucher\Logger\VoucherUsage;


use App\Voucher;

/**
 * Class VoucherUsageValueObject
 * @author Nima Ebrazi <nima.ebrazi@gmail.com>
 */
class VoucherUsageValueObject
{
    /**
     * @var Voucher
     */
    protected $voucher;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @var bool
     */
    protected $isWinner = true;


    /**
     * VoucherUsageValueObject constructor.
     * @param Voucher $voucher
     * @param string $phoneNumber
     */
    public function __construct($voucher = null, $phoneNumber = null)
    {
        $this->voucher = $voucher;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return static
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * @return Voucher
     */
    public function getVoucher()
    {
        return $this->voucher;
    }

    /**
     * @param Voucher $voucher
     * @return VoucherUsageValueObject
     */
    public function voucher($voucher): self
    {
        $this->voucher = $voucher;

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
     * @return self
     */
    public function phoneNumber($phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWinner(): bool
    {
        return $this->isWinner;
    }

    /**
     * @param bool $isWinner
     * @return self
     */
    public function setIsWinner(bool $isWinner): self
    {
        $this->isWinner = $isWinner;

        return $this;
    }
}