<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $id = 1;
    $id++;
    $student = App\StudentInfo::inRandomOrder()->first();
    $student->user_id = $id;
    $student->save();

    return [
        'id'              => $id,
        'google_id'       => $faker->randomNumber(),
        'student_info_id' => $student->id,
        'first_name'      => $student->first_name,
        'last_name'       => $student->last_name,
        'email'           => $student->email,
        'domain'          => 'ecrchs.org',
        'remember_token'  => str_random(10),
    ];
});
