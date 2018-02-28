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

    protected $user;
    protected $contract;

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show']]);
        $this->middleware('is.hirer', ['only' => ['store', 'update', 'destroy', 'getRequestList', 'enterContract']]);
    }

    private function isHirer() {
        if(Auth::user()->role==='hirer') {
            return true;
        }

        return false;
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
            throw new Exception('Role was not determined');
        }
        
        
        return ContractResource::collection($contracts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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


        return new ContractResource($contract);

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
        } catch(ModelNotFoundException $e) { 
            
        } 

        if($this->isHirer()) {

            return view('show.hirer_view', ['contract' => $contract, 'request' => $request]);

        }

        return view('show.freelancer_view')->with(['contract' => $contract, 'request' => $request]);
        
    }

    public function edit($id) {
        $contract = Contract::findOrFail($id);
        return view('test.edit')->with(['contract'=>$contract]);

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

        return new ContractResource($contract);
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
       
        if($contract->delete()) {
            return new ContractResource($contract);
        }

        throw new Exception("didn't delete");

    }

    public function browseContracts() {

        $contracts = Contract::where('status', 'open')->orderBy('created_at', 'desc')->get();

        return ContractResource::collection($contracts);
    }


    public function enterContract(Request $request, $id) 
    {
        $contract = Contract::findOrFail($id);

        try {
            $contract->freelancer_id = $request->input('id');
            $contract->freelancer = $request->input('name');
            $contract->freelancer_email = $request->input('email');
            $contract->assigned_at = Carbon::now();
            $contract->status = 'active';

            $contract->save();
        } catch(Exception $e) {
            return $e;
        }
        
        return response()->json(['success'=>'true', 'message' => 'Contract has been assigned']);
        

    
    }

    public function getRequestList() {
        
                $contractRequests = ContractRequest::where(['hirer_email' => Auth::user()->email, 'status' => 'sent'])->orderBy('created_at', 'desc')->get();
                
                return ContractResource::collection($contractRequests);
        
            }
}
