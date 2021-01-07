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

Route::get('package', 'TransactionController@index');
Route::get('package/{id}', 'TransactionController@show');  
Route::post('package', 'TransactionController@store'); 
Route::put('package/{id}', 'TransactionController@edit');
Route::patch('package/{id}', 'TransactionController@update');
Route::delete('package/{id}', 'TransactionController@delete');