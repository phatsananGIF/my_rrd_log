<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('rrdlog', 'RrdlogController@index');
Route::post('/rrdlog/viewlogRRD','RrdlogController@viewlogRRD');
Route::get('/rrdlog/downloadAll/{id}','RrdlogController@downloadfileAllRRD');
Route::get('/rrdlog/downloadcid/{id}','RrdlogController@downloadfileRRDcid');
Route::get('/rrdlog/deletefileselect/{id}',array('uses' => 'RrdlogController@deletefileselect'));
Route::get('/rrdlog/deletefileCID/{id}',array('uses' => 'RrdlogController@deletefileCID'));

Route::get('read', 'ReadallController@index');



Route::get('/test', function () {
    return view('testdatatable');
});