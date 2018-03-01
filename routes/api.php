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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('contracts', 'ContractController', ['except' => ['show']]);
Route::get('browse', 'ContractController@browseContracts');
Route::put('enter-contract/{id}', 'ContractController@enterContract');

Route::get('list-requests', 'ContractController@getRequestList');
Route::put('accept-request/{id}', 'RequestController@acceptRequest');
Route::put('reject-request/{id}', 'RequestController@acceptRequest');
Route::post('make-payment', 'ContractController@makePayment');