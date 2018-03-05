<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contract;
use App\Request as ContractRequest;
use App\Http\Resources\Contract as ContractResource;

class RequestController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function makeRequest($id) 
    {
        $contract = Contract::findOrFail($id);

        $hirerId = $contract->hirer_id;
        $hirerName = $contract->hirer;
        $hirerEmail = $contract->hirer_email;

        if(ContractRequest::where(['contract_id'=>$id, 'freelancer_id'=>Auth::user()->id])->count() > 0) 
            return response()->json(['success'=>false, 'message'=>"You've already requested this contract"]);


        $contractRequest = new ContractRequest;

        $contractRequest->contract_id = $id;
        $contractRequest->hirer_id = $hirerId;    
        $contractRequest->hirer_name = $hirerName;
        $contractRequest->hirer_email = $hirerEmail;
        $contractRequest->freelancer_id = Auth::user()->id;
        $contractRequest->freelancer_name = Auth::user()->name;
        $contractRequest->freelancer_email = Auth::user()->email;
        
        $contractRequest->status = 'sent';

        if($contractRequest->save()) {
            return response()->json(['success'=>true, 'message'=>'Request has been sent']);
        }

        abort(500, 'Error occured while making request');
        
        
    }

    public function getRequestList() {
        
        $contractRequests = ContractRequest::where(['hirer_email' => Auth::user()->email, 'status' => 'sent'])->orderBy('created_at', 'desc')->get();
        
        
        return ContractResource::collection($contractRequests);

    }
    
    public function acceptRequest($id)
    {
        try {
            $contractRequest = ContractRequest::findOrFail($id);
            $contractRequest->status = 'accepted';
            $contractRequest->save();
        } catch(ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Request entry could not be found']);
        }

        return response()->json(['success' => true, 'message' => 'Request has been accepted']);     
    }

    public function rejectRequest($id)
    {
        try {
            $contractRequest = ContractRequest::findOrFail($id); 
            if($contractRequest->status !== 'sent') {
                
                return response()->json(['success'=>false, 'message' => 'Request does not have a mutable status: ' . $contractRequest->status]); 
            } 
        } catch(ModelNotFoundException $e) {
            return response()->json(['success'=>false, 'message' => "Request entry could not be found"]); 
        }

        $contractRequest->status = 'rejected';
        
        $contractRequest->save();
        return response()->json(['success'=>true, 'message' => 'Request status has been updated: ' . $contractRequest->status]);   

        
        

    }

}
