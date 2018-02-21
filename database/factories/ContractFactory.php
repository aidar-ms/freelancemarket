<?php

use Faker\Generator as Faker;

function activeOrNot(Faker $faker) {
    if($faker->boolean(50)) {
        return 'Active';
    } else {
        return 'Not active';
    }
}

$factory->define(App\Contract::class, function (Faker $faker) {
    
    
    return [
        'freelancer' => $faker->name,
        'hirer' => $faker->name,
        'price' => $faker->biasedNumberBetween($min = 1, $max = 2000, $function = 'sqrt'),
        'status' => activeOrNot($faker),
        'assigned_at' => $faker->dateTime()
    ];
});


