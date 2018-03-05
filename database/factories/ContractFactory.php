<?php

use App\User;
use App\Contract;
use Faker\Generator as Faker;


function activeOrNot(Faker $faker, $freelancer) {

    if($faker->boolean(80)) {
        return ['freelancer_id'=>null, 'freelancer'=>null, 
                'freelancer_email'=>null, 'status'=>'open'];
    } else {
        return ['freelancer_id'=>$freelancer->id, 'freelancer'=>$freelancer->name, 
                'freelancer_email'=>$freelancer->email, 'status'=>'active'];
    }
}


$factory->define(App\Contract::class, function (Faker $faker) {
    
    /* $contract = ['title' => 'Test', 'description' => 'test test test', 'hirer_id' => $user->id, 
                     'hirer' => $user->name, 'hirer_email' => $user->email, 'price'=>100, 'status'=>'open',
                     'deadline_at' => '2020-12-20 20:20:20']; */
    $hirer = User::where(['role' => 'hirer'])->inRandomOrder()->first();
    $freelancer = User::where(['role' => 'freelancer'])->inRandomOrder()->first();
    $activeOrNot = activeOrNot($faker, $freelancer);
    
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'hirer_id' => $hirer->id,
        'hirer' => $hirer->name,
        'hirer_email' => $hirer->email,
        'freelancer_id' => $activeOrNot['freelancer_id'],
        'freelancer' => $activeOrNot['freelancer'],
        'freelancer_email' => $activeOrNot['freelancer_email'],
        'price' => $faker->biasedNumberBetween($min = 1, $max = 2000, $function = 'sqrt'),
        'status' => $activeOrNot['status'],
        'assigned_at' => $faker->dateTimeBetween('now', '+2 months'),
        'deadline_at' => $faker->dateTimeBetween('+2 months', '+6 months')
    ];
});


