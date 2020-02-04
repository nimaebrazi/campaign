<?php


namespace App\Infrastructure\Repository;


use App\Infrastructure\Repository\Cache\VoucherRepository as CacheVoucherRepository;
use App\Infrastructure\Repository\Cache\VoucherUsageLogRepository as VoucherUsageLogCacheRepository;
use App\Infrastructure\Repository\Contract\VoucherRepositoryInterface;
use App\Infrastructure\Repository\Contract\VoucherUsageLogRepository;
use App\Infrastructure\Repository\Eloquent\VoucherRepository as EloquentVoucherRepository;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(VoucherRepositoryInterface::class, function (Application $app) {

            return new CacheVoucherRepository(
                $app->make(EloquentVoucherRepository::class),
                $app->make(CacheRepository::class)
            );

        });

        $this->app->singleton(VoucherUsageLogRepository::class, function (Application $app) {
            return new VoucherUsageLogCacheRepository(
                $app->make(Factory::class)
            );
        });

        $this->app->singleton(VoucherUsageLogCacheRepository::class, function (Application $app) {
            return new VoucherUsageLogCacheRepository(
                $app->make(Factory::class)
            );
        });
    }

    public function provides()
    {
        return [
            VoucherRepositoryInterface::class,
            VoucherUsageLogRepository::class,
            VoucherUsageLogCacheRepository::class,
        ];
    }
}
