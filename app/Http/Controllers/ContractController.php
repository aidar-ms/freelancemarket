<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Contract as ContractResource;
use App\Contract;
use App\User;
use App\Request as ContractRequest;
use Carbon\Carbon;

class ContractController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show']]);
        $this->middleware('is.hirer', ['only' => ['store', 'update', 'destroy', 'enterContract']]);
    }

    private function isHirer() {
        return Auth::user()->role==='hirer' ? true : false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->isHirer()) {
            $contracts = Contract::where('hirer_email', Auth::user()->email)->orderBy('created_at', 'desc')->get();
        } elseif (!$this->isHirer()) {
            $contracts = Contract::where(['freelancer_email' => Auth::user()->email, 'status' => 'active'])->orderBy('created_at', 'desc')->get();
        } else {
            abort(500, 'Role was not determined');
        }
        
        
        return ContractResource::collection($contracts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contract = new Contract;

        $contract->title = $request->input('title');
        $contract->description = $request->input('description');
        $contract->hirer_id = auth()->user()->id;
        $contract->hirer = auth()->user()->name;
        $contract->hirer_email = auth()->user()->email;
        $contract->price = $request->input('price');
        $contract->status = 'open';
        $contract->deadline_at = $request->input('deadline');

        $contract->save();


        return ['success'=>true, 'message'=>'Contract was created'];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contract = Contract::findOrFail($id);
        $request = null;
        try {
            $request = ContractRequest::where(['contract_id' => $id, 'freelancer_email' => Auth::user()->email])->firstOrFail();
        } catch(ModelNotFoundException $e) {} 

        if($this->isHirer()) {

            return view('show.hirer_view', ['contract' => $contract, 'request' => $request]);

        }

        return view('show.freelancer_view')->with(['contract' => $contract, 'request' => $request]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $contract->title = $request->input('title');
        $contract->description = $request->input('description');
        $contract->price = $request->input('price');
        $contract->deadline_at = $request->input('deadline_at');

        $contract->save();

        return response()->json(['success'=>true, 'message'=>'Contract updated', 'contract' => $contract]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
       
        $contract->delete();
        
        return response()->json(['success' => true, 'message' => 'Contract deleted']);

    }

    public function browseContracts() {

        $contracts = Contract::where('status', 'open')->orderBy('created_at', 'desc')->get();

        return ContractResource::collection($contracts);
    }


    public function enterContract(Request $request, $id) 
    {
        $contract = Contract::findOrFail($id);

        $contract->freelancer_id = $request->input('id');
        $contract->freelancer = $request->input('name');
        $contract->freelancer_email = $request->input('email');
        $contract->assigned_at = Carbon::now();
        $contract->status = 'active';

        $contract->save();
        
        return response()->json(['success'=> true, 'message' => 'Contract has been assigned']);
   
    }

    public function makePayment(Request $request) {
        
        $contract = Contract::findOrFail(decrypt($request->input('contract_id')));
        $hirer = User::where(['email' => decrypt($request->input('hirer_email'))])->firstOrFail();
        $freelancer = User::where('email', decrypt($request->input('freelancer_email')))->firstOrFail();

        $price = $contract->price;

        if($hirer->balance - $price < 0) {
            return response()->json(['success' => false,'message' => 'Insufficient funds']);
        }

        $hirer->balance -= $price;
        $freelancer->balance += $price;
        $contract->status = 'closed';

        $contract->save();
        $hirer->save();
        $freelancer->save();


        return response()->json(['success'=> true, 'message' => 'Payment has been transferred']);

    }
}
