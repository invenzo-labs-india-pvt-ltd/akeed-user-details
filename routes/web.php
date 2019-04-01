<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1/'], function () use ($router) {
    $router->post('login',  ['uses' => 'Api\V1\LoginController@login']);
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('check-token',  ['uses' => 'Api\V1\LoginController@checkAuthToken']);
    });
});

/*Route::group(['middleware' => 'Api', 'prefix' => 'auth'], function ($router) {
    Route::group(['middleware' => 'V1'], function ($router) {
        Route::post('login', 'V1/LoginController@login');
    });
});*/