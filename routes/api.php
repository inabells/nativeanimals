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

Route::get('testDbConnection', "ApiController@testDbConnection");
Route::get('getAllPigs', "ApiController@getAllPigs");
Route::post('fetchNewPigRecord', "ApiController@fetchNewPigRecord");
Route::get('getViewSowPage', "ApiController@getViewSowPage");
Route::get('getAllSows', "ApiController@getAllSows");
Route::get('getAllBoars', "ApiController@getAllBoars");
Route::get('getAllFemaleGrowers', "ApiController@getAllFemaleGrowers");
Route::get('getAllMaleGrowers', "ApiController@getAllMaleGrowers");