<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('statusCompra');
            $table->string('codCliente');
            $table->string('cnpjCliente');
            $table->string('cnpjFilialBf');
            $table->string('codRca');
            $table->string('numPedido');
            $table->string('dataPedido');
            $table->string('valorTotalPedido');
            $table->string('canalVenda');
            $table->string('segmento');
            $table->datetime('criadoEm')->nullable()->default(NULL);;
            $table->datetime('atualizadoEm')->nullable()->default(NULL);;
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
