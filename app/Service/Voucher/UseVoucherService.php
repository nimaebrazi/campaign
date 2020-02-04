<?php


namespace App\Service\Voucher;


use App\Infrastructure\Repository\Contract\VoucherUsageLogRepository;
use App\Service\Voucher\Exception\VoucherException;
use App\Service\Voucher\Jobs\JobDispatcher;
use App\Service\Voucher\Logger\VoucherUsage\RedisVoucherUsageLogger;
use App\Service\Voucher\Logger\VoucherUsage\VoucherUsageValueObject;
use App\Service\Voucher\ValueObject\VoucherValueObject;
use App\Service\Voucher\Statistics\VoucherStatisticsInterface;
use App\Infrastructure\Repository\Contract\VoucherRepositoryInterface;
use App\Voucher;

/**
 * Class UseVoucherService
 * @author Nima Ebrazi <nima.ebrazi@gmail.com>
 */
class UseVoucherService
{
    /**
     * @var JobDispatcher
     */
    protected $jobDispatcher;

    /**
     * @var VoucherRepositoryInterface
     */
    protected $voucherRepository;

    /**
     * @var VoucherStatisticsInterface
     */
    protected $voucherStatistics;

    /**
     * @var VoucherUsageLogRepository
     */
    protected $voucherUsageLogRepository;

    /**
     * @var RedisVoucherUsageLogger
     */
    protected $redisVoucherUsageLogger;


    /**
     * UseVoucherService constructor.
     *
     * @param JobDispatcher $jobDispatcher
     * @param VoucherStatisticsInterface $voucherStatistics
     * @param VoucherRepositoryInterface $voucherRepository
     * @param RedisVoucherUsageLogger $redisVoucherUsageLogger
     * @param VoucherUsageLogRepository $voucherUsageLogRepository
     */
    public function __construct(
        JobDispatcher $jobDispatcher,
        VoucherStatisticsInterface $voucherStatistics,
        VoucherRepositoryInterface $voucherRepository,
        RedisVoucherUsageLogger $redisVoucherUsageLogger,
        VoucherUsageLogRepository $voucherUsageLogRepository
    ) {
        $this->jobDispatcher = $jobDispatcher;
        $this->voucherRepository = $voucherRepository;
        $this->voucherStatistics = $voucherStatistics;
        $this->redisVoucherUsageLogger = $redisVoucherUsageLogger;
        $this->voucherUsageLogRepository = $voucherUsageLogRepository;
    }

    /**
     * @param VoucherValueObject $voucherValueObject
     *
     * @return bool
     *
     * @throws Exception\VoucherCodeNotExistsException
     * @throws Exception\ExceededVoucherCodeUsageException
     * @throws Exception\FlagServiceException
     * @throws Exception\UsedBeforeException
     */
    public function apply(VoucherValueObject $voucherValueObject)
    {
        $this->errorIfServiceIsDisable();

        // find and cache
        $voucher = $this->voucherRepository->findByCode(
            $voucherValueObject->getCode()
        );

        // Exception if code not exists
        if (!$voucher) {
            throw VoucherException::voucherCodeNotExists();
        }


        // Check code used before
        $voucherUsageLogExists = $this->voucherUsageLogRepository->exists(
            $voucherValueObject->getCode(),
            $voucherValueObject->getPhoneNumber()
        );

        if ($voucherUsageLogExists) {
            throw VoucherException::usedBeforeException();
        }


        $voucherUsageModel = new VoucherUsageValueObject(
            $voucher, $voucherValueObject->getPhoneNumber()
        );

        // When exception throws we catch it for dispatching a job and rethrow it.
        if ($this->isVoucherExceededOfLimitUsage($voucher)) {
            $this->jobDispatcher->loserJob($voucherUsageModel);
            throw VoucherException::exceededVoucherCodeUsage();
        }

        // increment code in redis
        $this->voucherStatistics->increment($this->key(), $voucher->code);

        // log into redis
        $this->redisVoucherUsageLogger->log($voucherUsageModel);

        // Append phone and voucher code to winner queue.
        $this->jobDispatcher->winnerJob($voucherUsageModel);

        return true;
    }

    /**
     * @throws Exception\FlagServiceException
     */
    protected function errorIfServiceIsDisable()
    {
        if (config('campaign.voucher.storage_key') === false) {
            throw VoucherException::flagServiceException();
        }
    }

    /**
     * @param Voucher $voucher
     * @return bool
     */
    protected function isVoucherExceededOfLimitUsage($voucher)
    {
        $voucherCount = $this->voucherStatistics->count(
            $this->key(), $voucher->code
        );

        if ($voucherCount >= $voucher->limit) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return config('campaign.voucher.storage_key');
    }

}