<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    public function testNotAuthorizedAccess() {

        $this->get('/home')->assertRedirect('/login');

    }

    public function testAuthorizedAccess() {
        $hirer = User::where('role','hirer')->first();
        $freelancer = User::where('role','freelancer')->first();
        
        $this->actingAs($hirer)->get('/home')->assertViewIs('hirer.main');
        $this->actingAs($freelancer)->get('/home')->assertViewIs('freelancer.main');

    }
}
