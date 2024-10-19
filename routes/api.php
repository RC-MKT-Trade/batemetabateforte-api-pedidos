<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('apikey')->group(function () {


    Route::post('/pedido', function (Request $request) {
        return "pedidos salvos";
    });

    // Outras rotas
});
