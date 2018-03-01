<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'UserController@index')->name('home');

Route::get('/contracts/{id}', 'ContractController@show');
Route::view('browse', 'freelancer.browse');
Route::get('request/{id}', 'RequestController@makeRequest')->name('request');
//Route::post('/make-payment', 'UserController@makePayment');


Auth::routes();

