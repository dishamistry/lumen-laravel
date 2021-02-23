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

$router->group(['prefix' => 'api/v1', 'middleware' => []], function () use ($router) {
    $router->post('user/register', 'AuthController@register');
    $router->post('user/login', 'AuthController@login');
    $router->post('user/logout', 'AuthController@logout');
    $router->post('user/refresh-token', 'AuthController@refreshToken');
});

$router->group(['prefix' => 'api/v1', 'middleware' => ['jwt.auth']], function () use ($router) {
    // user routes
    $router->get('users', ['uses' => 'UserController@allUsers']);
    $router->get('user/{id}', 'UserController@show');
    $router->post('user/update/{id}', 'UserController@update');
    $router->post('user/delete/{id}', 'UserController@delete');

});
