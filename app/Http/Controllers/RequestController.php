<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contract;
use App\Request as ContractRequest;
use App\Http\Resources\Contract as ContractResource;
use App\Http\Resources\Request as RequestResource;

class RequestController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * List all active requests
     *
     * @return \Illuminate\Http\Response
     */
    public function list() {
        $contractRequests = ContractRequest::where(['hirer_email' => Auth::user()->email, 'status' => 'sent'])->orderBy('created_at', 'desc')->get();  
        
        return RequestResource::collection($contractRequests);
    }

    /**
     * Send a new request
     *
     * @return \Illuminate\Http\Response
     */
    public function send($contractId) 
    {
        $contract = Contract::findOrFail($contractId);

        if(ContractRequest::where(['contract_id'=>$contract->id, 'freelancer_id'=>Auth::user()->id])->count() > 0) 
            return response("Contract already requested", 403);

        $request = new ContractRequest;
        $request->contract_id = $contract->id;
        $request->hirer_id = $contract->hirer_id;    
        $request->hirer_name = $contract->hirer;
        $request->hirer_email = $contract->hirer_email;
        $request->freelancer_id = Auth::user()->id;
        $request->freelancer_name = Auth::user()->name;
        $request->freelancer_email = Auth::user()->email;
        $request->status = 'sent';

        DB::transaction(function() use($request) {
            
            $request->save();

        });
            

        return new RequestResource($request);

    }
    
    /**
     * Accept a request
     *
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {
        try {
            $request = ContractRequest::where(['status'=>'sent', 'id' => $id])->firstOrFail(); 
        } catch(ModelNotFoundException $e) {
            return response('Request status is not mutable', 403);
        }
        $request->status = 'accepted';
        $request->save();

        return new RequestResource($request);   
    }

    /**
     * Reject a request
     *
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        try {
            $request = ContractRequest::where(['status'=>'sent', 'id' => $id])->firstOrFail(); 
        } catch(ModelNotFoundException $e) {
            return response('Request status is not mutable', 403);
        }

        $request->status = 'rejected';
        $request->save();

        return new RequestResource($request);
    }

}
