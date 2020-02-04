<?php


namespace App\Service\Voucher\Statistics;


use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class VoucherStatisticsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->bind(VoucherStatisticsInterface::class, function (Application $app) {
            return new RedisVoucherStatistics(
                $app->make(RedisFactory::class)
            );
        });
    }

    public function provides()
    {
        return [
            VoucherStatisticsInterface::class
        ];
    }
}