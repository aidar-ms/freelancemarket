<?php 

namespace Tests\Unit;

use App\Request;
use App\User;
use App\Contract;
use Tests\TestCase;


use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;


class RequestControllerTest extends TestCase {
    /**
     * LEGEND
     * 
     * [H] - hirer method
     * [F] - freelancer method
     * [HF] - mixed method
     * 
     */

    /** LOCAL UTILITIES **/

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

        $requestedHirerIds = Request::select('hirer_id')->get();


        $callBack = function($user) use($requestedHirerIds) {
            return $requestedHirerIds->contains($user->id);
        };

        $nonRequestedHirer = User::where('role', 'hirer')->get()->diff($requestedHirerIds)->first();
        return $nonRequestedHirer;

    }
    public function getAnyFreelancer() {
        return User::where('role', 'freelancer')->inRandomOrder()->firstOrFail();
    }
    // ------------------------------------------------------------------------- //

    //**  TESTS **/

    // Check request listing features [H]
    public function testIfThereAreAnyRequests() {
        $this->assertGreaterThan(0, Request::count());
    }

    public function testListIfManyRequests() {
        
        $hirer = $this->getRequestedHirer();

        $allRequestsForThisHirer = Request::where(['hirer_id' => $hirer->id, 'status' => 'sent'])->count();

        $this->actingAs($hirer, 'api')->call('get', 'api/requests')->assertStatus(200)->assertJsonCount($allRequestsForThisHirer);
    }

    public function testListIfNoRequests() {
        
        $hirer = $this->getNonRequestedHirer();

        $response = $this->actingAs($hirer, 'api')->call('get', 'api/requests');

        $response->assertStatus(200)->assertJsonCount(0);

    }

    // Try to send a request to an open contract [F]
    public function testSend() {

        $contract = $this->getOpenContract();
        $freelancer = $this->getAnyFreelancer();

        $this->actingAs($freelancer, 'api')->call('get', 'api/requests/' . $contract->id . '/send' )->assertStatus(201);

        $this->assertDatabaseHas('requests', ['contract_id' => $contract->id, 'freelancer_id' => $freelancer->id, 'hirer_id' => $contract->hirer_id]);       

    }

    // Test if one request per contract rule applies [F]
    public function testSendIfRequestHasBeenMade() {
       
        $request = Request::inRandomOrder()->firstOrFail();
        $contract = Contract::where('id', $request->contract_id)->firstOrFail();
        $freelancer = User::where(['role'=>'freelancer', 'id'=>$request->freelancer_id])->firstOrFail();

        $this->actingAs($freelancer, 'api')->call('get', 'api/requests/' . $contract->id . '/send' )->assertStatus(403);
    }

    // Accept a request [H]
    public function testAccept() {

        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();
        $freelancer = User::where('id', $request->freelancer_id)->first();

        $this->actingAs($hirer, 'api')->call('get', 'api/requests/' . $request->id . '/accept')->assertStatus(200);
        $this->assertDatabaseHas('requests', ['id'=>$request->id, 'hirer_id'=>$hirer->id, 'freelancer_id'=>$freelancer->id, 'status' => 'accepted']);

    }

    // Accept a non-existing request [H]
    public function testAcceptNonExisting() {

        $nullRequestId = rand(100, 1000);

        $hirer = User::where('role', 'hirer')->inRandomOrder()->first();

        $this->actingAs($hirer, 'api')->call('get', 'api/requests/' . $nullRequestId . '/accept')->assertStatus(403);
    }

    // Reject a request [H]
    public function testReject() {
        $request = Request::where('status', 'sent')->inRandomOrder()->first();
        $hirer = User::where('id', $request->hirer_id)->first();
        $freelancer = User::where('id', $request->freelancer_id)->first();

        if($this->actingAs($hirer, 'api')->call('get', 'api/requests/' . $request->id . '/reject')->assertStatus(200)) {
            $this->assertDatabaseHas('requests', ['id'=>$request->id, 'hirer_id'=>$hirer->id, 'freelancer_id'=>$freelancer->id, 'status' => 'rejected']);
        }
        

    }

    // Reject a non-existing request [H]
    public function testRejectNonExisting() {

        $nullRequestId = rand(100, 1000);
        
        $hirer = User::where('role', 'hirer')->inRandomOrder()->first();

        $this->actingAs($hirer, 'api')->call('get', 'api/requests/' . $nullRequestId . '/reject')->assertStatus(403);

    }

    // Reject a non-pending request [H]
    public function testRejectNonPendingRequest() {

        $request = Request::where('status', '<>', 'sent')->inRandomOrder()->first();

        $hirer = User::where('id', $request->hirer_id)->first();

        $this->actingAs($hirer, 'api')->call('get', 'api/requests/' . $request->id . '/reject')->assertStatus(403);

    }

}







?>