<?php

use Faker\Generator as Faker;

$factory->define(App\Freelancer::class, function (Faker $faker) {
    return [
        'name' =>  $faker->name,
        'email' => $faker->email,
        'password' => $faker->password,
        'balance' => $faker->biasedNumberBetween($min = 1, $max = 2000, $function = 'sqrt')
    ];
});
