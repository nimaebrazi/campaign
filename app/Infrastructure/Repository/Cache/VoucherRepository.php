<?php


namespace App\Infrastructure\Repository\Cache;


use App\Infrastructure\Repository\BaseRepository;
use App\Infrastructure\Repository\Contract\VoucherRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class VoucherRepository extends BaseRepository implements VoucherRepositoryInterface
{
    /**
     * @var VoucherRepositoryInterface
     */
    protected $voucherRepository;

    /**
     * @var CacheRepository
     */
    protected $cacheRepository;

    /**
     * VoucherRepository constructor.
     * @param VoucherRepositoryInterface $voucherRepository
     * @param CacheRepository $cacheRepository
     */
    public function __construct(
        VoucherRepositoryInterface $voucherRepository,
        CacheRepository $cacheRepository
    ) {
        $this->voucherRepository = $voucherRepository;
        $this->cacheRepository = $cacheRepository;
    }


    /**
     * @param $code
     * @return mixed
     */
    public function findByCode($code)
    {
        return $this->cacheRepository->rememberForever("voucher:{$code}", function () use ($code) {
            return $this->voucherRepository->findByCode($code);
        });
    }
}