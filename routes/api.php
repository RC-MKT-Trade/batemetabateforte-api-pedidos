<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('apikey')->group(function () {


Route::post('/pedido', [\App\Http\Controllers\PedidoController::class, 'salvarPedidos']);
});
