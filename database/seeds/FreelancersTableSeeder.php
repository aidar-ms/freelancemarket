<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreelancersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Freelancer::class, 30)->create();
    }
}
