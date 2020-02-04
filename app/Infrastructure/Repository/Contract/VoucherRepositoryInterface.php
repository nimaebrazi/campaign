<?php


namespace App\Infrastructure\Repository\Contract;

/**
 * Interface VoucherRepositoryInterface
 * @author Nima Ebrazi <nima.ebrazi@gmail.com>
 */
interface VoucherRepositoryInterface
{
    /**
     * @param string $code
     * @return mixed
     */
    public function findByCode($code);
}