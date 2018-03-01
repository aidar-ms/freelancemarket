<?php

namespace Tests\Unit;

use App\User;
use App\Contract;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ExampleTest extends TestCase
{
    //use RefreshDatabase;


    public function getHirer() {
        $hirer = User::find(2);

        return $hirer;
    }

    public function getFreelancer() {
        $freelancer = User::find(3);

        return $freelancer;

    }


    private function getContractByEmail($email) {
        return Contract::where(['hirer_email'=>$email]);
    }

    /**
     * Log in system test.
     *
     * @return void
     */
    public function testLogIn() {

       $credentials = [
            'email' => 'johndoe@john.doe',
            'password' => '123456'
       ];

       $response = $this->post('login', $credentials)->assertRedirect('/');
    }

    /**
     * Test of login system with wrong password
     *
     * @return void
     */

    public function testWrongPass() {
        $credentials = [
            'email' => 'johndoe@john.doe',
            'password' => 'wrongpass'
       ];

       $response = $this->post('login', $credentials)->assertSessionHasErrors();

    }
    /**
     * Test store new contract functionality
     *
     * @return void
     */

     public function testStore() 
     {
        $user = $this->getHirer();
        $userEmail = $user->email;
        $latestContract;

        $newContract = ['title' => 'Test', 'description' => 'test test test', 'price'=>100,
                     'deadline_at' => '2020-12-20 20:20:20']; 

        $storeResponse = $this->actingAs($this->getHirer(), 'api')->post('/api/contracts', $newContract)->assertJson(['success'=>true]);

        $latestContract = $this->getContractByEmail($userEmail)->orderBy('created_at', 'desc')->first();

        $this->assertEquals($latestContract->title, $newContract['title']);
     }

     /* Test index() action */

     public function testIndex() {

        $user = $this->getHirer();
        $userEmail = $user->email;

        $numberOfUserContracts = $this->getContractByEmail($userEmail)->count();       
        $indexResponse = $this->actingAs($this->getHirer(), 'api')->call('get', '/api/contracts')->assertStatus(200)->assertJsonCount($numberOfUserContracts);

     }

    /* Test update() action */

    public function testUpdate() {
        $user = $this->getHirer();
        $userEmail = $this->getHirer()->email;
        $updatedContract = ['title' => 'Updated Test', 'description' => 'test test test', 'price'=>100,
                     'deadline_at' => '2020-12-20 20:20:20']; 

        $latestContract = $this->getContractByEmail($userEmail)->orderBy('created_at', 'desc')->first();
        $editResponse = $this->actingAs($user, 'api')->call('put', '/api/contracts/' . $latestContract->id, $updatedContract)->assertStatus(200)->assertJson(['success'=>true]);
       
        $this->assertEquals(Contract::find($latestContract->id)->title, $updatedContract['title']);
    }
        

     /* Test show() action */

     public function testShow() {

        $user = $this->getHirer();
        $userEmail = $user->email;
        $randomContract = $this->getContractByEmail($userEmail)->inRandomOrder()->first();  
        $showResponse = $this->actingAs($this->getHirer(), 'web')->call('get', '/contracts/' . $randomContract->id)->assertStatus(200)->assertViewIs('show.hirer_view')->assertViewHas('contract', $randomContract);       

     }

     /* Test destroy() action */

     public function testDestroy() {

        $user = $this->getHirer();
        $userEmail = $user->email;

        $latestContract = $this->getContractByEmail($userEmail)->orderBy('created_at', 'desc')->first();
        $latestContractId = $latestContract->id;
        $destroyResponse = $this->actingAs($user, 'api')->call('delete', 'api/contracts/' . $latestContractId)->assertStatus(200)->assertJson(['success'=>true]); 

        $this->assertDatabaseMissing('contracts', ['id'=>$latestContractId]);
     }


     /* Test browseContracts() action */

     public function testBrowseContracts() {

        $freelancer = $this->getFreelancer();
        $freelancerId = $freelancer->id;
        $freelancerEmail = $freelancer->email;

        $openContractsCount = Contract::where(['status'=>'open'])->count();

        $browse = $this->actingAs($freelancer, 'api')->call('get', 'api/browse')->assertStatus(200)->assertJsonCount($openContractsCount);

     }

    /* Test enterContract() action */

    public function testEnterContract() {

        $hirer = $this->getHirer();
        $hirerId = $hirer->id;
        $hirerEmail = $hirer->email;

        $freelancer = $this->getFreelancer();

        $freelancerInfo = ['id' => $freelancer->id, 'name' => $freelancer->name, 'email' => $freelancer->email];

        $randomOpenContract = Contract::where(['hirer_id'=>$hirerId, 'status'=>'open'])->first();
        $openContractId = $randomOpenContract->id;
        $this->actingAs($hirer, 'api')->call('put', 'api/enter-contract/'.$openContractId, $freelancerInfo)->assertStatus(200)->assertJson(['success'=>true]);

        $this->assertDatabaseHas('contracts', ['id'=>$openContractId, 'freelancer_id'=>$freelancerInfo['id'], 'freelancer'=>$freelancerInfo['name'], 
                                               'freelancer_email' => $freelancerInfo['email']]);


    }

    public function testMakePayment() {
        $hirer = $this->getHirer();
        $hirerInitBalance = $hirer->balance;
        $hirerEmail = $hirer->email;

        $freelancer = $this->getFreelancer();
        $freelancerInitBalance = $freelancer->balance;
        $freelancerEmail = $freelancer->email;

        $contract = Contract::where(['status'=>'active', 'hirer_email' => $hirerEmail, 
                                     'freelancer_email'=>$freelancerEmail])->first();
        $contractId = $contract->id;
        $contractPrice = $contract->price;
        
        $paymentRequest = ['contract_id'=>encrypt($contractId), 'hirer_email' => encrypt($hirerEmail), 'freelancer_email' => encrypt($freelancerEmail)];
        $this->actingAs($hirer, 'api')->call('post', 'api/make-payment', $paymentRequest)->assertStatus(200);

        $newHirerBalance = User::where(['email'=>$hirerEmail])->first()->balance;
        $newFreelancerBalance = User::where(['email'=>$freelancerEmail])->first()->balance;
        $newContractInstance = Contract::where(['id'=>$contractId, 'hirer_email' => $hirerEmail, 
                                               'freelancer_email'=>$freelancerEmail])->first();

        $this->assertEquals($hirerInitBalance-$contractPrice, $newHirerBalance);
        $this->assertEquals($freelancerInitBalance+$contractPrice, $newFreelancerBalance);
        $this->assertTrue($newContractInstance->status === 'closed');

    }
}
