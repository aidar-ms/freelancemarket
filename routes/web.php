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

//Route::view('/contract-page/{id}', 'contract-page');
//Route::post('/make-payment', 'UserController@makePayment');


Auth::routes();

