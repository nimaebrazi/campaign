<?php


namespace App\Service\Voucher\Logger\VoucherUsage;


use App\Service\Voucher\Logger\VoucherUsage\Contract\VoucherUsageLogger;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class VoucherUsageLoggerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseVoucherUsageLogger::class, function () {
            return new DatabaseVoucherUsageLogger();
        });

        $this->app->singleton(RedisVoucherUsageLogger::class, function (Application $app) {
            return new RedisVoucherUsageLogger(
                $app->make(RedisFactory::class)
            );
        });

        $this->app->singleton(VoucherUsageLogger::class, function () {
            return new DatabaseVoucherUsageLogger();
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            VoucherUsageLogger::class
        ];
    }
}