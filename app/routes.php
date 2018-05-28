<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


// =============================================
// API ROUTES ==================================
// =============================================

//Permet la connexion, création et déconnexion d'un compte utilisateur
Route::get('/api/users/logout', array('before' => 'token', 'uses' => 'AuthController@logout'));
Route::post('/api/users/login', 'AuthController@login');
Route::post('/api/users/', 'UserController@store');

Route::get('/api/bets/distances', 'BetController@distances');


Route::post('/api/users/join', array('before' => 'token', 'uses' => 'UserController@joinRoom'));

Route::get('/api/games/{id?}/bets', array('before' => 'token', 'uses' => 'GameController@getBets'));

//Tout les ressources disponibles avec un token
Route::group(array('prefix' => 'api', 'before' => 'token'), function() {

    Route::resource('users', 'UserController',
        array('only' => array('index', 'show', 'update')));

    Route::resource('games', 'GameController',
        array('only' => array('index', 'show')));

    Route::resource('transactions', 'TransactionController',
        array('only' => array('index', 'show')));

    Route::resource('groups', 'GroupController',
        array('only' => array('index', 'show')));

    Route::resource('teams', 'TeamController',
        array('only' => array('index', 'show')));

    Route::resource('bbts', 'BetBonusTypeController',
        array('only' => array('index', 'show')));

    Route::resource('bets', 'BetController',
        array('only' => array('index', 'show', 'store', 'update')));

    Route::resource('bets_bonus', 'BetBonusController',
        array('only' => array('index', 'show', 'store')));

    Route::resource('bracket', 'BracketController',
        array('only' => array('index')));

    Route::resource('roles', 'RoleController',
        array('only' => array('index', 'show')));
});

Route::post('/api/admin/login', array('before' => ['token', 'admin'], 'uses' => 'AuthController@loginWithoutPassword'));
Route::post('/api/admin/register', array('before' => ['token', 'admin'], 'uses' => 'UserController@storeWithoutPassword'));
Route::get('/api/admin/users', array('before' => 'token', 'uses' => 'UserController@indexAdmin'));

// =============================================
// CATCH ALL ROUTE =============================
// =============================================
// all routes that are not home or api will be redirected to the frontend
// this allows angular to route them
App::missing(function()
{
    return View::make('index');
});
