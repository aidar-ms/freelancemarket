<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contract;
use App\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        if(Auth::user()->role === 'freelancer') {

            return view('freelancer.main');
        } else if(Auth::user()->role === 'hirer') {
           
            return view('hirer.main');
        }

        throw new Exception('Role not defined');
        
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function makePayment(Request $request) {

        $contract = Contract::findOrFail(decrypt($request->input('contract_id')));
        
        $hirer = User::where(['email' => decrypt($request->input('hirer_email'))])->firstOrFail();
        $freelancer = User::where('email', decrypt($request->input('freelancer_email')))->firstOrFail();

        $price = $contract->price;

        if($hirer->balance - $price < 0) {
            return view('hirer.response')->with(['response' => 'Insufficient funds']);
        }

        $hirer->balance -= $price;
        $freelancer->balance += $price;
        $contract->status = 'closed';

        $contract->save();
        $hirer->save();
        $freelancer->save();


        return view('hirer.response')->with(['response' => 'Payment has been transferred']);


        


    }
}
