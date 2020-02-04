<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\VoucherUsageLog;
use Faker\Generator as Faker;

$factory->define(VoucherUsageLog::class, function (Faker $faker) {

    $voucher = factory(\App\Voucher::class)->create();

    return [
        'voucher_code' => $voucher->code,
        'phone_number' => $faker->phoneNumber,
        'voucher_id'   => factory(\App\Voucher::class)->create()
    ];

});
