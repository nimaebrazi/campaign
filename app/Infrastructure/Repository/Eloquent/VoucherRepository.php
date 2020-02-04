<?php


namespace App\Infrastructure\Repository\Eloquent;


use App\Infrastructure\Repository\Contract\VoucherRepositoryInterface;
use App\Voucher;
use Illuminate\Support\Collection;

class VoucherRepository implements VoucherRepositoryInterface
{
    /**
     * @param string $code
     * @return Collection
     */
    public function findByCode($code)
    {
        return Voucher::where('code', '=', $code)->first();
    }
}