<?php

use App\User;
use App\Contract;
use Faker\Generator as Faker;


function acceptedOrNot(Faker $faker) {

    if($faker->boolean(50)) {
        return 'sent';
    } else {
        return 'rejected';
    }
}


$factory->define(App\Request::class, function (Faker $faker) {
    
    /* $contract = ['title' => 'Test', 'description' => 'test test test', 'hirer_id' => $user->id, 
                     'hirer' => $user->name, 'hirer_email' => $user->email, 'price'=>100, 'status'=>'open',
                     'deadline_at' => '2020-12-20 20:20:20']; */

    $contract = Contract::where(['status'=>'open'])->inRandomOrder()->first();
    $hirer = User::where('id', $contract->hirer_id)->first();
    $freelancer = User::where('role', 'freelancer')->inRandomOrder()->first();
    $requestedOrNot = $faker->boolean(50);
    
    return [
        'contract_id' => $contract->id,
        'hirer_id' => $hirer->id,
        'hirer_name' => $hirer->name,
        'hirer_email' => $hirer->email,
        'freelancer_id' => $freelancer->id,
        'freelancer_name' => $freelancer->name,
        'freelancer_email' => $freelancer->email,
        'status' => $requestedOrNot ? 'sent' : 'rejected',
    ];
    
});
