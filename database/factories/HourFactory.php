<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Hour::class, function (Faker $faker) {
    $start = $faker->dateTimeBetween('-10 days'); //Start time
    $end = $faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s') . " +12 hours"); //End time

    return [
        'user_id'    => 1,
        'student_id' => 115602,
        'event_id'   => $faker->numberBetween(0, 5),
        'start_time' => $start,
        'end_time'   => $end,
        'comments'   => ($faker->boolean) ? $faker->realText() : null
    ];
});
