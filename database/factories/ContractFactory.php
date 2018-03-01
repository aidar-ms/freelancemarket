<?php

use App\User;
use App\Contract;
use Faker\Generator as Faker;

function activeOrNot(Faker $faker) {
    if($faker->boolean(80)) {
        return 'open';
    } else {
        return 'closed';
    }
}

$factory->define(App\Contract::class, function (Faker $faker) {
    
    /* $contract = ['title' => 'Test', 'description' => 'test test test', 'hirer_id' => $user->id, 
                     'hirer' => $user->name, 'hirer_email' => $user->email, 'price'=>100, 'status'=>'open',
                     'deadline_at' => '2020-12-20 20:20:20']; */
    $user = User::where(['role' => 'hirer'])->inRandomOrder()->first();
    
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'hirer_id' => $user->id,
        'hirer' => $user->name,
        'hirer_email' => $user->email,
        'price' => $faker->biasedNumberBetween($min = 1, $max = 2000, $function = 'sqrt'),
        'status' => activeOrNot($faker),
        'assigned_at' => $faker->dateTimeBetween('now', '+2 months'),
        'deadline_at' => $faker->dateTimeBetween('+2 months', '+6 months')
    ];
});


