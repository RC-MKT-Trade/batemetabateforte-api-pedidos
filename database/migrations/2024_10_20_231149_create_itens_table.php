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
        Schema::create('itens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('idPedido')->constrained('pedidos');
            $table->string('chaveSefaz');
            $table->string('descCompleta');
            $table->string('ean');
            $table->string('quantProdutoUnidade');
            $table->string('quantProdutoCaixa');
            $table->string('valorProdutoUnidade');
            $table->string('valorTotalProduto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens');
    }
};
