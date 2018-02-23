<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\Contract as ContractResource;
use App\Contract;
use App\User;

class ContractController extends Controller
{

    protected $user;
    protected $contract;

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['show']]);
        $this->middleware('is.hirer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userContracts = Contract::where('email', Auth::user()->email)->orderBy('created_at', 'desc')->get();
        
        return ContractResource::collection($userContracts);
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
        $contract->price = $request->input('price');
        $contract->email = auth()->user()->email;
        $contract->hirer = auth()->user()->name;
        $contract->hirer_id = auth()->user()->id;
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

        return view('show.contract', ['contract' => $contract]);
        
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
}
