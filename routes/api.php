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

Route::post('/register', [\App\Http\Controllers\UsersController::class, 'Register']);
Route::post('/login', [\App\Http\Controllers\UsersController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('add-store', [\App\Http\Controllers\StoresController::class, 'addStore']);
    Route::post('set-vat', [\App\Http\Controllers\VatController::class, 'setVat']);
    Route::post('set-shipping', [\App\Http\Controllers\ShippingController::class, 'setShipping']);
    Route::post('set-product', [\App\Http\Controllers\ProductsController::class, 'setProduct']);
    Route::post('add-to-cart', [\App\Http\Controllers\CartsController::class, 'addToCart']);
    Route::post('calculate-cart', [\App\Http\Controllers\CartsController::class, 'calculateCart']);
});
