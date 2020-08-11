<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::get('cep/{cep}', 'CepController')->name('cep');
Route::post('/status', 'PedidoController@receberStatus');

Auth::routes();

Route::prefix('pagamento')->name('pagamento.')->group(function () {
    Route::get('/boleto', 'PedidoController@exibirBoleto')->name('boleto');
    Route::post('/boleto/processamento', 'PedidoController@processarBoleto')->name('processamento.boleto');
    Route::get('/cartao', 'PedidoController@exibirCartao')->name('cartao');
    Route::post('/cartao/processamento', 'PedidoController@processarCartao')->name('processamento.cartao');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
