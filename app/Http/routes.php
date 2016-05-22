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

Route::post('/login', 'Api\AuthenticateController@authenticate');

Route::group([
    'middleware' => 'jwt.auth',
    'namespace' => 'Api\V1',
], function($app) {
    Route::resource('posts', 'PostController');
    Route::resource('boards', 'BoardController');
	Route::resource('bills', 'BillController');
});
