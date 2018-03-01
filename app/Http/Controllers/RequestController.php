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
    public function makeRequest($id) 
    {
        $contract = Contract::findOrFail($id);

        $hirerId = $contract->hirer_id;
        $hirerName = $contract->hirer;
        $hirerEmail = $contract->hirer_email;

        try {
            if(ContractRequest::where(['contract_id'=>$id, 'freelancer_id'=>Auth::user()->id])->count() > 0) {
                $response = 'Already requested';
                return view('freelancer.response')->with('response', $response);
            }
        } catch(ModelNotFoundException $e) {

        }
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
            $response = 'Request sent';
            return view('freelancer.response')->with('response', $response);

        }
        
        
    }
    
    public function acceptRequest($id)
    {
        $contractRequest = ContractRequest::findOrFail($id);

        try {
            $contractRequest->status = 'accepted';
            $contractRequest->save();
        } catch(Exception $e) {
            return $e;
        }

        return response()->json(['success'=>'true', 'message' => 'Request has been accepted']);

        
    }

    public function rejectRequest($id)
    {
        $contractRequest = ContractRequest::findOrFail($id);

        

        try {
            $contractRequest->status = 'rejected';
            $contractRequest->save();
        } catch(Exception $e) {
            return $e;
        }

        return response()->json(['success'=>'true', 'message' => 'Request has been rejected']);

        
    }

}
