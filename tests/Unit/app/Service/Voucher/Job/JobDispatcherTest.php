<?php


namespace Tests\Unit\app\Service\Voucher\Job;


use App\Service\Voucher\Jobs\JobDispatcher;
use App\Service\Voucher\Jobs\LogVoucherUsageJob;
use App\Service\Voucher\Logger\VoucherUsage\VoucherUsageValueObject;
use App\Voucher;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;


class JobDispatcherTest extends TestCase
{

    /**
     * @var JobDispatcher
     */
    protected $jobDispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->jobDispatcher = new JobDispatcher();

    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    function it_should_dispatch_job_on_loser_queue()
    {
        Queue::fake();

        $this->jobDispatcher->loserJob(
            $this->makeVoucherUsageValueObject()
        );

        // Assert a job was pushed to a given queue...
        Queue::assertPushed(LogVoucherUsageJob::class);
        Queue::assertPushedOn('voucher-loser-queue', LogVoucherUsageJob::class);
    }

    public function makeVoucherUsageValueObject()
    {
        $voucher = factory(Voucher::class)->make([
            'code'  => '12345',
            'limit' => 5
        ]);

        return new VoucherUsageValueObject(
            $voucher, '09127654321'
        );
    }

    /** @test */
    function it_should_dispatch_job_on_winner_queue()
    {
        Queue::fake();

        $this->jobDispatcher->winnerJob(
            $this->makeVoucherUsageValueObject()
        );

        Queue::assertPushed(LogVoucherUsageJob::class);
        Queue::assertPushedOn('voucher-winner-queue', LogVoucherUsageJob::class);

    }

}