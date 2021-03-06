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
use App\Http\Controllers\Ui\UiAdmController;
use App\Http\Controllers\Ui\UiAllianceController;
use App\Http\Controllers\Ui\UiEventsController;
use App\Http\Controllers\Ui\UiController;
use App\Http\Controllers\Ui\UiGalaxyController;
use App\Http\Controllers\Ui\UiPlayerController;
use App\Http\Middleware\LogRoute;

//use App\Http\Controllers\Ui\UiSearchController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'ui', 'namespace' => '\\', 'middleware' => LogRoute::class], function () use ($router) {
    $router->get('/', ['as' => 'main', function () {
        return view('players');
    }]);

    // Search Player
    $router->get('/players', ['as' => 'main.players', 'uses' => UiPlayerController::class . '@search']);
    $router->post('/players', ['as' => 'main.players', 'uses' => UiPlayerController::class . '@search']);
    // Player
    $router->get('players/{id}', ['as' => 'player', 'uses' => UiPlayerController::class . '@player']);
    $router->post('players/{id}', ['as' => 'player', 'uses' => UiPlayerController::class . '@player']);

    // Galaxy
    $router->get('galaxy/{gal}:{sys}', ['as' => 'galaxy.view', 'uses' => UiGalaxyController::class . '@view']);
    $router->post('galaxy/{gal}:{sys}', ['as' => 'galaxy.view', 'uses' => UiGalaxyController::class . '@viewPost']);
    $router->get('galaxy[/{gal}]', ['as' => 'galaxy', 'uses' => UiGalaxyController::class . '@index']);

    // Alliance
    $router->get('alliance/{id}', ['as' => 'alliance', 'uses' => UiAllianceController::class . '@index']);

    // Events
    $router->get('events[/{period}]', ['as' => 'events', 'uses' => UiEventsController::class . '@index']);
    $router->post('events[/{period}]', ['as' => 'events', 'uses' => UiEventsController::class . '@index']);

    // Administrative
    $router->get('adm', ['as' => 'adm', 'uses' => UiAdmController::class . '@index']);
});

$router->group(['prefix' => '/api', 'namespace' => '\\', 'middleware' => LogRoute::class], function () use ($router) {
    $router->get('test', ApiController::class . '@test');

    $router->post('updateSystem', ApiController::class . '@updateSystem');

    $router->post('updateMessages', ApiController::class . '@updateMessages');
});
