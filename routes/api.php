<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

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

// Obtener listado de bancos
Route::get('get-bancos/{comercio_id}', [APIController::class, 'getBancos']);

// Obtener listado de metodos de pago de esos bancos
Route::get('get-metodos-pago/{comercio_id}', [APIController::class, 'getMetodoPago']);

// Obtener listado de puntos de venta
Route::get('get-puntos-venta/{comercio_id}', [APIController::class, 'getPuntosVentaMount']);

// Obtener listado de saldo en cuenta corriente de cada cliente
Route::get('get-cta-cte/{cliente_id}', [APIController::class, 'GetCtaClienteClienteById']);

// Obtener listado de clientes
Route::get('get-clientes/{casa_central_id}', [APIController::class, 'getClientes']);

// Obtener listado de Datos de listas de precios
Route::get('get-lista-precios/{casa_central_id}', [APIController::class, 'getListaPrecios']);

// Obtener PRODUCTOS 
Route::get('get-productos/{casa_central_id}', [APIController::class, 'getProductos']);

// Obtener PRECIOS de todas las listas
Route::get('get-precios/{casa_central_id}', [APIController::class, 'getPrecios']);

// Obtener STOCKS de todas las sucursales
Route::get('get-stocks/{casa_central_id}', [APIController::class, 'getStocks']);

// Obtener precio de un producto individual
Route::get('get-product-precio/{product_id}/{variacion}/{lista_id}', [APIController::class, 'getProductPrecioProduct']);

// Obtener stock de un producto individual
Route::get('get-product-stock/{product_id}/{variacion}/{sucursal_id}/{casa_central_id}', [APIController::class, 'getProductStockProduct']);