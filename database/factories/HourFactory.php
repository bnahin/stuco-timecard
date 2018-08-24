<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Hour::class, function (Faker $faker) {
    static $marked = 0;

    $needsReview = false;
    if ($marked < 3) {
        if ($faker->numberBetween(0, 10) == 7) {
            $needsReview = true;
            $marked++;
        }
    }
    $start = $faker->dateTimeBetween('-10 months'); //Start time
    $end = $faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s') . " +12 hours"); //End time

    return [
        'student_id' => 18,
        'event_id'   => $faker->numberBetween(0, 5),
        'start_time' => $start,
        'end_time'   => $end,
        'club_id'    => 1,
        'needs_review' => $needsReview,
        'comments'   => ($faker->boolean) ? $faker->realText() : null
    ];
});
