<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
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
        $this->middleware('auth:api');
        $this->middleware('is.hirer', ['only' => ['store', 'update', 'destroy', 'enter', 'close']]);
    }

    private function isHirer() {
        return Auth::user()->role==='hirer' ? true : false;
    }

    protected function transferPayment($contract) {
        
        if(!is_a($contract, 'App\Contract')) return false;

        $price = $contract->price;
        $hirer = User::where('email', $contract->hirer_email)->firstOrFail();
        $freelancer = User::where('email', $contract->freelancer_email)->firstOrFail();

        if($hirer->balance - $price < 0) return false;

        $hirer->balance -= $contract->price;
        $freelancer->balance += $contract->price;
        $contract->status = 'closed';

        DB::transaction(function() use($contract, $hirer, $freelancer) {
            $contract->save(); 
            $hirer->save(); 
            $freelancer->save();
        });

        return true;

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

        DB::transaction(function() use($contract) {
            $contract->save(); 
        });


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
        $request = ContractRequest::where(['contract_id' => $id, 'freelancer_email' => Auth::user()->email])->first();

        return new ContractResource($contract);
        
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
       
        $contract->delete();
        
        return new ContractResource($contract);

    }

    public function browse() {

        $contracts = Contract::where('status', 'open')->orderBy('created_at', 'desc')->get();

        return ContractResource::collection($contracts);
    }


    public function enter(Request $request, $id) 
    {
        $contract = Contract::findOrFail($id);

        $contract->freelancer_id = $request->input('id');
        $contract->freelancer = $request->input('name');
        $contract->freelancer_email = $request->input('email');
        $contract->assigned_at = Carbon::now();
        $contract->status = 'active';

        $contract->save();
        
        return new ContractResource($contract);
   
    }

    public function close($id) {
        $contract = Contract::findOrFail($id);

        return $this->transferPayment($contract) ? new ContractResource($contract) : response('Insufficient funds', 403);
    }
}
