<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')
    ->middleware('jwt.auth')
    ->group(function () {

        //rotas de autenticação
        Route::post('logout', 'App\Http\Controllers\AuthController@logout');
        Route::post('me', 'App\Http\Controllers\AuthController@me');
        Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');

        //rotas Sistemas
        Route::apiResource('/pais', 'App\Http\Controllers\ControllerPais');
        Route::post('/pais/{id}', 'App\Http\Controllers\ControllerPais@getById');

        Route::apiResource('/estado', 'App\Http\Controllers\ControllerEstado');
        Route::post('/estado/{id}', 'App\Http\Controllers\ControllerEstado@getById');
        
        Route::apiResource('/cidade', 'App\Http\Controllers\ControllerCidade');
        Route::post('/cidade/{id}', 'App\Http\Controllers\ControllerCidade@getById');

        Route::apiResource('/categorias', 'App\Http\Controllers\ControllerCategorias');
        Route::post('/categorias/{id}', 'App\Http\Controllers\ControllerCategorias@getById');

        Route::apiResource('/fornecedor', 'App\Http\Controllers\ControllerFornecedor');
        Route::post('/fornecedor/{id}', 'App\Http\Controllers\ControllerFornecedor@getById');

        Route::apiResource('/formaspagamento', 'App\Http\Controllers\ControllerFormasPagamento');
        Route::post('/formaspagamento/{id}', 'App\Http\Controllers\ControllerFormasPagamento@getById');
        
        Route::apiResource('/cliente', 'App\Http\Controllers\ControllerCliente');
        Route::post('/cliente/{id}', 'App\Http\Controllers\ControllerCliente@getById');
        
        Route::apiResource('/servico', 'App\Http\Controllers\ControllerServico');
        Route::post('/servico/{id}', 'App\Http\Controllers\ControllerServico@getById');
        
        Route::apiResource('/profissional', 'App\Http\Controllers\ControllerProfissional');
        Route::post('/profissional/{id}', 'App\Http\Controllers\ControllerProfissional@getById');
    });

Route::post('login', 'App\Http\Controllers\AuthController@login');

