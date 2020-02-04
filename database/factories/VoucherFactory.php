<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Voucher;
use Faker\Generator as Faker;

$factory->define(Voucher::class, function (Faker $faker) {

    return [
        'code'    => (string)$faker->unique()->numberBetween(10000, 99999),
        'limit'   => $faker->numberBetween(1, 100),
    ];

});
