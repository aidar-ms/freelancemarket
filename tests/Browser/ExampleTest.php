<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends DuskTestCase
{
    use RefreshDatabase;
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))->visit('/home')->assertSee('Your contracts')->click('@create-contract')->whenAvailable('.modal', function($modal) {
                $modal->assertSee('Create new contract');
            });
        });
    }


}
