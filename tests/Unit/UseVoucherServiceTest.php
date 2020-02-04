<?php

namespace Tests\Unit;

use App\Infrastructure\Repository\Contract\VoucherRepositoryInterface;
use App\Infrastructure\Repository\Contract\VoucherUsageLogRepository;
use App\Service\Voucher\Exception\ExceededVoucherCodeUsageException;
use App\Service\Voucher\Exception\UsedBeforeException;
use App\Service\Voucher\Exception\VoucherCodeNotExistsException;
use App\Service\Voucher\Jobs\JobDispatcher;
use App\Service\Voucher\Jobs\LogVoucherUsageJob;
use App\Service\Voucher\Logger\VoucherUsage\RedisVoucherUsageLogger;
use App\Service\Voucher\Statistics\VoucherStatisticsInterface;
use App\Service\Voucher\UseVoucherService;
use App\Service\Voucher\ValueObject\VoucherValueObject;
use App\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;


class UseVoucherServiceTest extends TestCase
{
    /** @var UseVoucherService */
    protected $useVoucherService;

    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface $jobDispatcherMock
     */
    protected $jobDispatcherMock;

    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface $voucherRepositoryMock
     */
    protected $voucherRepositoryMock;

    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface $voucherStatisticsMock
     */
    protected $voucherStatisticsMock;

    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface $voucherStatisticsMock
     */
    protected $redisVoucherUsageLoggerMock;

    /**
     * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface $voucherStatisticsMock
     */
    protected $voucherUsageLogRepositoryMock;


    public function setUp(): void
    {
        parent::setUp();

        $this->createDependencyMock();

        // Create service with mocked dependency
        $this->useVoucherService = new UseVoucherService(
            $this->jobDispatcherMock,
            $this->voucherStatisticsMock,
            $this->voucherRepositoryMock,
            $this->redisVoucherUsageLoggerMock,
            $this->voucherUsageLogRepositoryMock
        );
    }

    protected function createDependencyMock()
    {
        $this->jobDispatcherMock = Mockery::mock(JobDispatcher::class);
        $this->voucherStatisticsMock = Mockery::mock(VoucherStatisticsInterface::class);
        $this->voucherRepositoryMock = Mockery::mock(VoucherRepositoryInterface::class);
        $this->redisVoucherUsageLoggerMock = Mockery::mock(RedisVoucherUsageLogger::class);
        $this->voucherUsageLogRepositoryMock = Mockery::mock(VoucherUsageLogRepository::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    function it_should_throws_exception_when_code_not_exists()
    {
        $v = $this->makeVoucherValueObject();

        $this->voucherRepositoryMock->shouldReceive('findByCode')->once()->andReturn(null);
        $this->expectException(VoucherCodeNotExistsException::class);

        $this->useVoucherService->apply($v);
    }

    /**
     * @return VoucherValueObject
     */
    protected function makeVoucherValueObject()
    {
        return VoucherValueObject::make()
            ->code('12345')
            ->phoneNumber('09127654321');
    }

    /** @test */
    function it_should_return_false_when_voucher_is_exeeded_limit_usage()
    {
        $voucher = factory(Voucher::class)->make([
            'limit' => 10,
            'code'  => '12345'
        ]);


        $this->voucherStatisticsMock->shouldReceive('count')->once()->andReturn(11);

        $result = $this->invokeMethodWithInternalException(
            $this->useVoucherService, 'isVoucherExceededOfLimitUsage', [$voucher]
        );

        $this->assertTrue($result);
    }

    /** @test */
    function it_should_has_not_error_when_usage_count_is_less_than_voucher_code_limit()
    {
        $voucher = factory(Voucher::class)->make([
            'limit' => 10,
            'code'  => '12345'
        ]);


        $this->voucherStatisticsMock->shouldReceive('count')->once()->andReturn(9);


        $result = $this->invokeMethodWithInternalException(
            $this->useVoucherService, 'isVoucherExceededOfLimitUsage', [$voucher]
        );

        $this->assertFalse($result);
    }

    /** @test */
    public function it_should_log_a_phone_number_and_code_in_loser_queue()
    {
        $v = $this->makeVoucherValueObject();

        $voucher = factory(Voucher::class)->make([
            'limit' => 10,
            'code'  => $v->getCode()
        ]);


        $this->voucherRepositoryMock->shouldReceive('findByCode')->once()->andReturn($voucher);
        $this->voucherUsageLogRepositoryMock->shouldReceive('exists')->once()->andReturn(false);
        $this->voucherStatisticsMock->shouldReceive('count')->once()->andReturn(11);
        $this->jobDispatcherMock->shouldReceive('loserJob')->once();
        $this->expectException(ExceededVoucherCodeUsageException::class);


        $this->useVoucherService->apply($v);

    }

    /** @test */
    public function it_should_throw_exception_when_user_used_voucher_code_before()
    {
        $v = $this->makeVoucherValueObject();

        $voucher = factory(Voucher::class)->make([
            'limit' => 10,
            'code'  => $v->getCode()
        ]);


        $this->voucherRepositoryMock->shouldReceive('findByCode')->once()->andReturn($voucher);
        $this->voucherUsageLogRepositoryMock->shouldReceive('exists')->once()->andReturn(true);

        $this->expectException(UsedBeforeException::class);

        $this->useVoucherService->apply($v);
    }

    /** @test */
    public function it_should_log_a_phone_number_and_code_in_winner_queue()
    {
        $v = $this->makeVoucherValueObject();

        $voucher = factory(Voucher::class)->make([
            'limit' => 10,
            'code'  => $v->getCode()
        ]);


        $this->voucherRepositoryMock->shouldReceive('findByCode')->once()->andReturn($voucher);
        $this->voucherUsageLogRepositoryMock->shouldReceive('exists')
            ->once()->with($v->getCode(), $v->getPhoneNumber())->andReturn(false);
        $this->voucherStatisticsMock->shouldReceive('count')->once()->andReturn(2);
        $this->jobDispatcherMock->shouldReceive('winnerJob')->once();
        $this->voucherStatisticsMock->shouldReceive('increment')->once();
        $this->redisVoucherUsageLoggerMock->shouldReceive('log')->once();

        $result = $this->useVoucherService->apply($v);

        $this->assertTrue($result);
    }

}
