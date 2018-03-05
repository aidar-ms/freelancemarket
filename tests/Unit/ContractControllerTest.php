<?php

namespace Tests\Unit;

use App\User;
use App\Contract;
use Tests\TestCase;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\RefreshDatabase;

class ContractControllerTest extends TestCase
{

    public function getAnyHirer() {
        $hirer = User::where(['role' => 'hirer'])->inRandomOrder()->firstOrFail();

        return $hirer;
    }

    public function getAnyFreelancer() {
        $freelancer = User::where(['role' => 'freelancer'])->inRandomOrder()->firstOrFail();

        return $freelancer;
    }

    public function getOpenHirer() {
        $openHirerName = Contract::where('status','open')->inRandomOrder()->firstOrFail()->hirer;

        $openHirer = User::where(['name' => $openHirerName])->inRandomOrder()->firstOrFail();

        return $openHirer;
    }

    public function getActiveHirer() {
        $activeHirerName = Contract::where('status','active')->inRandomOrder()->firstOrFail()->hirer;
        
        $activeHirer = User::where(['name'=>$activeHirerName])->inRandomOrder()->firstOrFail();

        return $activeHirer;

    }

    public function getActiveFreelancer() {
        $activeFreelancerName = Contract::where('status', 'active')->inRandomOrder()->firstOrFail()->freelancer;

        $activeFreelancer = User::where(['name'=>$activeFreelancerName])->inRandomOrder()->firstOrFail();

        return $activeFreelancer;

    }


    public function getContractByEmail($user, $email) {

        if($user->role === 'hirer') {
            return Contract::where(['hirer_email'=>$email]);
        } elseif($user->role === 'freelancer') {
           return Contract::where(['freelancer_email'=>$email]);
        } 

        abort(500, 'Invalid role: ' . $user->role);
    }

    public function testIsHirerMiddleware() {
        $contract = Contract::where('status', 'open')->inRandomOrder()->first();
        $updatedContract = ['title' => 'Updated Test', 'description' => 'test test test', 'price'=>100,
                            'deadline_at' => '2020-12-20 20:20:20']; 
        $freelancer = User::where('role', 'freelancer')->first();

        $this->actingAs($freelancer, 'api')->call('put', 'api/contracts/' . $contract->id, $updatedContract)->assertStatus(403);
    }

     /* Test index() action [HF] */

     public function testIndexForHirers() {
        
        $hirer = $this->getOpenHirer();

        $numberOfUserContracts = $this->getContractByEmail($hirer, $hirer->email)->count();       
        $indexResponse = $this->actingAs($hirer, 'api')->call('get', '/api/contracts')->assertStatus(200)->assertJsonCount($numberOfUserContracts);

    }

    public function testIndexForFreelancers() {
        
        $freelancer = $this->getActiveFreelancer();

        $numberOfUserContracts = $this->getContractByEmail($freelancer, $freelancer->email)->count();       
        $indexResponse = $this->actingAs($freelancer, 'api')->call('get', '/api/contracts')->assertStatus(200)->assertJsonCount($numberOfUserContracts);

    }

    /* Test store method [H] */

    public function testStore() 
    {
       $hirer = $this->getAnyHirer(); $latestContract = null;

       $newContract = ['title' => 'Test', 'description' => 'test test test', 'price'=>100,
                    'deadline_at' => '2020-12-20 20:20:20']; 

       $storeResponse = $this->actingAs($hirer, 'api')->post('/api/contracts', $newContract)->assertJson(['success'=>true]);

       // Get the last added contract in and confirm that it has the same title as the one just created
       $latestContract = $this->getContractByEmail($hirer, $hirer->email)->orderBy('created_at', 'desc')->first();

       $this->assertEquals($latestContract->title, $newContract['title']);
    }

    /* Test update() action */

    public function testUpdate() {
        $hirer = $this->getOpenHirer();
        $updatedContract = ['title' => 'Updated Test', 'description' => 'test test test', 'price'=>100,
                        'deadline_at' => '2020-12-20 20:20:20']; 

        $latestContract = $this->getContractByEmail($hirer, $hirer->email)->orderBy('created_at', 'desc')->first();

        // Edit the latest created contract
        $editResponse = $this->actingAs($hirer, 'api')->call('put', '/api/contracts/' . $latestContract->id, $updatedContract)->assertStatus(200)->assertJson(['success'=>true]);
        
        //Assert that the title of the latest created contract has updated title
        $this->assertEquals(Contract::find($latestContract->id)->title, $updatedContract['title']);
    }
        


    /* Test destroy() action [HF] */

    public function testDestroy() {

        $hirer = $this->getOpenHirer();

        $latestContract = $this->getContractByEmail($hirer, $hirer->email)->orderBy('created_at', 'desc')->first();
        $latestContractId = $latestContract->id;
        $destroyResponse = $this->actingAs($hirer, 'api')->call('delete', 'api/contracts/' . $latestContractId)->assertStatus(200)->assertJson(['success'=>true]); 

        // Assert database doesn't have the contract that was just deleted

        $this->assertDatabaseMissing('contracts', ['id'=>$latestContractId]);
    }


    /* Test browseContracts() action [F] */

    public function testBrowseContracts() {

        $freelancer = $this->getAnyFreelancer();

        $openContractsCount = Contract::where(['status'=>'open'])->count();

        $browse = $this->actingAs($freelancer, 'api')->call('get', 'api/browse')->assertStatus(200)->assertJsonCount($openContractsCount);

    }

    /* Test enterContract() action [H] */

    public function testEnterContract() {

        $hirer = $this->getOpenHirer();
        $hirerId = $hirer->id;
        $hirer->email = $hirer->email;

        $freelancer = $this->getAnyFreelancer();

        $freelancerInfo = ['id' => $freelancer->id, 'name' => $freelancer->name, 'email' => $freelancer->email];

        $randomOpenContract = Contract::where(['hirer_id'=>$hirer->id, 'status'=>'open'])->first();
        $randomOpenContractId = $randomOpenContract->id;
        $this->actingAs($hirer, 'api')->call('put', 'api/enter-contract/'.$randomOpenContractId, $freelancerInfo)->assertStatus(200)->assertJson(['success'=>true]);

        $this->assertDatabaseHas('contracts', ['id'=>$randomOpenContractId, 'freelancer_id'=>$freelancerInfo['id'], 'freelancer'=>$freelancerInfo['name'], 
                                                'freelancer_email' => $freelancerInfo['email'], 'status'=>'active']);

    }

    /* Test show() action [HF] */

    public function testShowForHirers() {
        
        $hirer = $this->getOpenHirer();
        $randomContract = $this->getContractByEmail($hirer, $hirer->email)->inRandomOrder()->first();  
        $showResponse = $this->actingAs($hirer, 'web')->call('get', '/contracts/' . $randomContract->id)->assertStatus(200)->assertViewIs('show.hirer_view')->assertViewHas('contract', $randomContract);       

    }

    public function testShowForFreelancers() {
        
        $freelancer = $this->getActiveFreelancer();
        $randomContract = $this->getContractByEmail($freelancer, $freelancer->email)->inRandomOrder()->first();  
        $showResponse = $this->actingAs($freelancer, 'web')->call('get', '/contracts/' . $randomContract->id)->assertStatus(200)->assertViewIs('show.freelancer_view')->assertViewHas('contract', $randomContract);       

    }

    public function testMakePayment() {

        $contract = Contract::where(['status'=>'active'])->firstOrFail();
        
        $paymentRequest = ['contract_id'=>encrypt($contract->id), 'hirer_email' => encrypt($contract->hirer_email), 'freelancer_email' => encrypt($contract->freelancer_email)];

        $hirer = User::where('email', $contract->hirer_email)->first();
        $initHirerBalance = $hirer->balance;

        $freelancer = User::where('email', $contract->freelancer_email)->first();
        $initFreelancerBalance = $freelancer->balance;
        
        if($this->actingAs($hirer, 'api')->call('post', 'api/make-payment', $paymentRequest)->assertStatus(200)->assertJson(['success'=>true])) {

            $newHirerBalance = User::where(['email'=>$hirer->email])->first()->balance;
            $newFreelancerBalance = User::where(['email'=>$freelancer->email])->first()->balance;
            $newContractInstance = Contract::where(['id'=>$contract->id, 'hirer_email' => $hirer->email, 
                                                    'freelancer_email'=>$freelancer->email])->first();
    
            $this->assertEquals($initHirerBalance - $contract->price, $newHirerBalance);
            $this->assertEquals($initFreelancerBalance + $contract->price, $newFreelancerBalance);
            $this->assertTrue($newContractInstance->status === 'closed');

        }

    }

    public function testInsufficientFunds() {

        // Jack up a contract's price
        $contract = Contract::where(['status' => 'active'])->inRandomOrder()->first();
        $contract->price = 100000;
        $contract->save();
        

        $requestData = ['contract_id'=>encrypt($contract->id), 'hirer_email' => encrypt($contract->hirer_email), 'freelancer_email' => encrypt($contract->freelancer_email)];
        
        $hirer = User::where('email', $contract->hirer_email)->first();

        $this->actingAs($hirer, 'api')->call('post', 'api/make-payment', $requestData)->assertStatus(200)->assertJson(['success'=>false, 'message' => 'Insufficient funds']);

    }

     
}
