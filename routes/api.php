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
Route::get('getAnimalProperties', "ApiController@getAnimalProperties");
Route::get('getGroupingProperties', "ApiController@getGroupingProperties");
Route::post('fetchGrossMorphology',"ApiController@fetchGrossMorphology");
Route::post('fetchMorphometricCharacteristics',"ApiController@fetchMorphometricCharacteristics");
Route::get('getAllCount', "ApiController@getAllCount");
Route::post('fetchWeightRecords',"ApiController@fetchWeightRecords");
Route::post('addAsBreeder',"ApiController@addAsBreeder");
Route::post('addMortalityRecord', "ApiController@addMortalityRecord");
Route::post('addSalesRecord', "ApiController@addSalesRecord");
Route::post('addRemovedAnimalRecord', "ApiController@addRemovedAnimalRecord");
Route::get('getMortalityPage', "ApiController@getMortalityPage");
Route::get('getSalesPage', "ApiController@getSalesPage");
Route::get('getOthersPage', "ApiController@getOthersPage");
Route::get('searchPig',"ApiController@searchPig");
Route::get('searchSows',"ApiController@searchSows");
Route::get('searchBoars',"ApiController@searchBoars");
Route::post('addBreedingRecord',"ApiController@addBreedingRecord");
Route::get('getBreedingRecord',"ApiController@getBreedingRecord");
Route::get('findGroupingId', "ApiController@findGroupingId");
Route::get('getAddSowLitterRecordPage', "ApiController@getAddSowLitterRecordPage");

