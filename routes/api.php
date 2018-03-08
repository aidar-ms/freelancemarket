<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/test', function(){
    dd( Request::header());
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('contracts', 'ContractController');
Route::put('contracts/{id}/enter', 'ContractController@enter');
Route::get('contracts/{id}/close', 'ContractController@close');

Route::get('browse', 'ContractController@browse');
//Route::post('contracts/pay', 'ContractController@makePayment');

Route::get('requests', 'RequestController@list');
Route::get('requests/{id}/send', 'RequestController@send');
Route::get('requests/{id}/accept', 'RequestController@accept');
Route::get('requests/{id}/reject', 'RequestController@reject');