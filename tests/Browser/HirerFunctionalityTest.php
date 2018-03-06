<?php

namespace Tests\Browser;

use App\User;
use App\Contract;
use App\Request;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends DuskTestCase
{

    protected function getRandomHirer() {
       return User::where('role', 'hirer')->inRandomOrder()->first();
    }
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testHomePageOpening()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::findOrFail(1))->visit('/home')->assertSee('Your contracts');});
        
    }

    public function testCreateContract() {
        $hirer = $this->getRandomHirer();

        $this->browse(function (Browser $browser) use($hirer) {
            $browser->loginAs($hirer)->visit('/home')->click('@create-contract')->whenAvailable('.modal', function($modal) {
                $modal->assertSee('Create new contract')->type('#title', 'Test title')
                                                        ->type('#description', 'Test description')
                                                        ->type('#price', '200')
                                                        ->keys('#deadline', '3022020')
                                                        ->keys('#deadline', '{arrow_right}')
                                                        ->keys('#deadline', '1010A')
                                                        ->press('#submit');
                                                        
                                                          
                
            })->pause(2000)->assertDialogOpened('Contract successfully posted')->acceptDialog();          
            
        }); 
    }

    public function testEditContract() {

        $contract = Contract::inRandomOrder()->first();
        $hirer = User::where('id', $contract->hirer_id)->first();

        $this->browse(function (Browser $browser) use($hirer) {
            $browser->loginAs($hirer)->visit('/home')->waitFor('@edit-contract', 1)->click('@edit-contract')->whenAvailable('#editContractModal', function($modal) {
                $modal->assertSee('Edit contract')->append('title', 'Edited title')->append('description', 'Edited desc')->press('submit');
            })->pause(2000)->assertDialogOpened('Contract edited')->acceptDialog();     
        });
    }

    public function testDeleteContract() {

        $contract = Contract::inRandomOrder()->first();
        $hirer = User::where('id', $contract->hirer_id)->first();

        $this->browse(function (Browser $browser) use($hirer) {
            $browser->loginAs($hirer)->visit('/home')->waitFor('@delete-contract', 1)->click('@delete-contract')
                                                     ->pause(1000)->assertDialogOpened('Contract deleted')->acceptDialog();     
        });
    }

    public function testAcceptRequest() {
        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();

        $this->browse(function(Browser $browser) use($hirer) {
            $browser->loginAs($hirer)->visit('/home')->waitFor('@accept-request', 1)->click('@accept-request')
                                                     ->pause(1000)->assertDialogOpened('Message from server: Contract has been assigned')->acceptDialog(); 
        });

    }

    public function testRejectRequest() {
        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();

        $this->browse(function(Browser $browser) use($hirer) {
            $browser->loginAs($hirer)->visit('/home')->waitFor('@reject-request', 1)->click('@reject-request')
                                                     ->pause(1000)->assertDialogOpened('Message from server: Request has been rejected')->acceptDialog(); 
        });

    }

}
