<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Booking::class, function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->userId,
        'start' => rand(time(), time()+1209600),
        'end' => rand(time()+1209600, time()+2419200),
        'costs' => rand(5, 25),
        'comments' => str_random(150),
        'persons' => rand(1, 15)
    ];
});
