<?php 

namespace Tests\Unit;

use App\Request;
use App\User;
use App\Contract;
use Tests\TestCase;


class RequestControllerTest extends TestCase {

    public function getOpenContract() {
        return Contract::where('status', 'open')->inRandomOrder()->firstOrFail();
    }

    public function getActiveContract() {
        return Contract::where('status', 'active')->inRandomOrder()->firstOrFail();
    }

    public function getOpenHirer() {
        
        $openHirerId = Contract::where('status','open')->inRandomOrder()->firstOrFail()->hirer_id;

        return User::where('id', $openHirerId)->firstOrFail();
    }

    public function getRequestedHirer() {
        $sentRequest = Request::where('status', 'sent')->inRandomOrder()->firstOrFail();

        return User::where('id', $sentRequest->hirer_id)->firstOrFail();
    }

    public function getNonRequestedHirer() {

        $requestedHirerIds = Request::select('hirer_id')->where('status', 'sent')->get();


        $callBack = function($user) use($requestedHirerIds) {
            return $requestedHirerIds->contains($user->id);
        };

        $nonRequestedHirer = User::where('role', 'hirer')->inRandomOrder()->get()->reject($callBack)->first();
        return $nonRequestedHirer;

    }
    public function getAnyFreelancer() {
        return User::where('role', 'freelancer')->inRandomOrder()->firstOrFail();
    }

    public function testIfThereAreAnyRequests() {
        $this->assertGreaterThan(0, Request::count());
    }

    public function testGetRequestListIfManyRequests() {
        
        $hirer = $this->getRequestedHirer();

        $allRequestsForThisHirer = Request::where('hirer_id', $hirer->id)->count();

        $this->actingAs($hirer, 'api')->call('get', 'api/list-requests')->assertStatus(200)->assertJsonCount($allRequestsForThisHirer);
    }

    public function testGetRequestListIfNoRequests() {
        
        $hirer = $this->getNonRequestedHirer();

        $this->actingAs($hirer, 'api')->call('get', 'api/list-requests')->assertStatus(200)->assertJsonCount(0);

    }

    public function testMakeRequest() {

        $contract = $this->getOpenContract();
        $freelancer = $this->getAnyFreelancer();

        $this->actingAs($freelancer, 'api')->call('get', 'api/make-request/' . $contract->id )->assertStatus(200)->assertJson(['success'=>true]);

        $this->assertDatabaseHas('requests', ['contract_id' => $contract->id, 'freelancer_id' => $freelancer->id, 'hirer_id' => $contract->hirer_id]);       

    }

    public function testMakeRequestIfRequestHasBeenMade() {
       
        $request = Request::inRandomOrder()->firstOrFail();
        $contract = Contract::where('id', $request->contract_id)->firstOrFail();
        $freelancer = User::where(['role'=>'freelancer', 'id'=>$request->freelancer_id])->firstOrFail();

        $this->actingAs($freelancer, 'api')->call('get', 'api/make-request/' . $contract->id )->assertStatus(200)->assertJson(['success'=>false]);
    }

    public function testAcceptRequest() {

        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();
        $freelancer = User::where('id', $request->freelancer_id)->first();

        $this->actingAs($hirer, 'api')->call('put', 'api/accept-request/' . $request->id)->assertStatus(200)->assertJson(['success'=>true]);
        $this->assertDatabaseHas('requests', ['id'=>$request->id, 'hirer_id'=>$hirer->id, 'freelancer_id'=>$freelancer->id, 'status' => 'accepted']);

    }

    public function testAcceptNonExistingRequest() {

        $nullRequestId = rand(100, 1000);

        $hirer = User::where('role', 'hirer')->inRandomOrder()->first();

        $this->actingAs($hirer, 'api')->call('put', 'api/accept-request/' . $nullRequestId)->assertStatus(200)->assertJson(['success'=>false]);
    }

    public function testRejectRequest() {
        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();
        $freelancer = User::where('id', $request->freelancer_id)->first();

        if($this->actingAs($hirer, 'api')->call('put', 'api/reject-request/' . $request->id)->assertStatus(200)->assertJson(['success'=>true])) {
            $this->assertDatabaseHas('requests', ['id'=>$request->id, 'hirer_id'=>$hirer->id, 'freelancer_id'=>$freelancer->id, 'status' => 'rejected']);
        }
        

    }

    public function testRejectNonExistingRequest() {

        $nullRequestId = rand(100, 1000);
        
        $hirer = User::where('role', 'hirer')->inRandomOrder()->first();

        $this->actingAs($hirer, 'api')->call('put', 'api/reject-request/' . $nullRequestId)->assertStatus(200)->assertJson(['success'=>false, 'message' => 'Request entry could not be found']);

    }

    public function testRejectNonPendingRequest() {

        $request = Request::where('status', '<>', 'sent')->inRandomOrder()->first();

        $hirer = User::where('id', $request->hirer_id)->first();

        $this->actingAs($hirer, 'api')->call('put', 'api/reject-request/' . $request->id)->assertStatus(200)->assertJson(['success'=>false, 'message' => 'Request does not have a mutable status: ' . $request->status]);

    }

}







?>