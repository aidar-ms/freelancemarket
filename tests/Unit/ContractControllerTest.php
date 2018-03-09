<?php

namespace Tests\Unit;

use App\User;
use App\Contract;
use Tests\TestCase;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

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
        $activeFreelancerId = Contract::where('status', 'active')->inRandomOrder()->firstOrFail()->freelancer_id;

        $activeFreelancer = User::findOrFail($activeFreelancerId);

        return $activeFreelancer;

    }


    public function getContractsById($user, $userId) {
        
        if($user->role === 'hirer') {
            return Contract::where(['hirer_id'=>$userId]);
        } elseif($user->role === 'freelancer') {
           return Contract::where(['freelancer_id'=>$userId]);
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

        $numberOfUserContracts = $this->getContractsById($hirer, $hirer->id)->count();       
        $indexResponse = $this->actingAs($hirer, 'api')->call('get', '/api/contracts')->assertStatus(200)->assertJsonCount($numberOfUserContracts);

    }

    public function testIndexForFreelancers() {
        
        $freelancer = $this->getActiveFreelancer();

        $numberOfUserContracts = $this->getContractsById($freelancer, $freelancer->id)->count();       
        $indexResponse = $this->actingAs($freelancer, 'api')->call('get', '/api/contracts')->assertStatus(200)->assertJsonCount($numberOfUserContracts);

    }

    /* Test store method [H] */

    public function testStore() 
    {
       $hirer = $this->getAnyHirer(); 

       $newContract = ['title' => 'Test', 'description' => 'test test test', 'price'=>100,
                    'deadline_at' => '2020-12-20 20:20:20']; 

       $storeResponse = $this->actingAs($hirer, 'api')->post('/api/contracts', $newContract)->assertStatus(201);

    }

    /* Test update() action */

    public function testUpdate() {

        $hirer = $this->getOpenHirer();
        $updatedContract = ['title' => 'Updated Test', 'description' => 'test test test', 'price'=>100,
                        'deadline_at' => '2020-12-20 20:20:20']; 

        $latestContract = $this->getContractsById($hirer, $hirer->id)->orderBy('created_at', 'desc')->first();

        // Edit the latest created contract
        $editResponse = $this->actingAs($hirer, 'api')->call('put', '/api/contracts/' . $latestContract->id, $updatedContract)->assertStatus(200);
        
    }
        


    /* Test destroy() action [HF] */

    public function testDestroy() {

        $hirer = $this->getOpenHirer();

        $latestContract = $this->getContractsById($hirer, $hirer->id)->orderBy('created_at', 'desc')->first();
        $latestContractId = $latestContract->id;
        $destroyResponse = $this->actingAs($hirer, 'api')->call('delete', 'api/contracts/' . $latestContractId)->assertStatus(200); 

    }


    /* Test browseContracts() action [F] */

    public function testBrowseContracts() {

        $freelancer = $this->getAnyFreelancer();

        $openContractsCount = Contract::where('status', 'open')->count();

        $this->actingAs($freelancer, 'api')->call('get', 'api/browse')->assertStatus(200)->assertJsonCount($openContractsCount);

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

        $this->actingAs($hirer, 'api')->call('put', 'api/contracts/'.$randomOpenContractId.'/enter', $freelancerInfo)->assertStatus(200);

    }

    public function testEnterNonOpenContract() {

        $contract = Contract::where('status','<>','open')->inRandomOrder()->first();

        $hirer = User::findOrFail($contract->hirer_id);
        $freelancer = $this->getAnyFreelancer();

        $freelancerInfo = ['id' => $freelancer->id, 'name' => $freelancer->name, 'email' => $freelancer->email];

        $this->actingAs($hirer, 'api')->call('put', 'api/contracts/'.$contract->id.'/enter', $freelancerInfo)->assertStatus(403);

    }

    /* Test show() action [HF] */

/*   public function testShowForHirers() {
        
        $hirer = $this->getOpenHirer();
        $randomContract = $this->getContractsById($hirer, $hirer->id)->inRandomOrder()->first();  
        $showResponse = $this->actingAs($hirer, 'api')->call('get', 'api/contracts/' . $randomContract->id)->assertStatus(200);    

    }

    public function testShowForFreelancers() {
        
        $freelancer = $this->getActiveFreelancer();
        $randomContract = $this->getContractsById($freelancer, $freelancer->id)->inRandomOrder()->first();  
        $showResponse = $this->actingAs($freelancer, 'api')->call('get', 'api/contracts/' . $randomContract->id)->assertStatus(200);

    }*/

    public function testClose() {

        $contract = Contract::where(['status'=>'active'])->inRandomOrder()->get()->reject(function($contract) {
            User::where('id', $contract->hirer_id)->firstOrFail()->balance < $contract->price;
        })->first();
        

        $hirer = User::where('email', $contract->hirer_email)->firstOrFail();
        //$initHirerBalance = $hirer->balance;

        $freelancer = User::where('email', $contract->freelancer_email)->firstOrFail();
        //$initFreelancerBalance = $freelancer->balance;
        
        $this->actingAs($hirer, 'api')->call('get', 'api/contracts/' . $contract->id . '/close')->assertStatus(200);

    }

    public function testInsufficientFunds() {

        // Jack up a contract's price
        $contract = Contract::where(['status' => 'active'])->inRandomOrder()->first();
        $contract->price = 100000;
        $contract->save();
        
        $hirer = User::where('email', $contract->hirer_email)->first();

        $this->actingAs($hirer, 'api')->call('get', 'api/contracts/' . $contract->id . '/close')->assertStatus(403);

    }

     
}
