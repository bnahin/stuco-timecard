<?php

use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    static $order = 1;

    return [
        'event_name' => 'Event ' . $order . ' Name',
        'is_active'  => 1,
        'order'      => $order++
    ];
});
