<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Ui\UiController;
use App\Http\Controllers\Ui\UiGalaxyController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'ui', 'namespace' => '\\'], function () use ($router) {
    $router->get('/', ['as' => 'main', function () {
        return view('index');
    }]);
    $router->get('galaxy', ['as' => 'galaxy', 'uses' => UiGalaxyController::class . '@index']);
    $router->get('changelog', ['as' => 'changelog', function () {
        return view('index');
    }]);
});


$router->group(['prefix' => '/api', 'namespace' => '\\'], function () use ($router) {
    $router->post('updateSystem', ApiController::class . '@updateSystem');
});
