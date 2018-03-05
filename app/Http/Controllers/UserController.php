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

        abort(500, 'Role not defined');
        
    }
   
}
