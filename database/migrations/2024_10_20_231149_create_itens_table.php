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
            $table->string('chaveSefaz', 255);
            $table->string('descCompleta', 255);
            $table->string('ean', 255);
            $table->integer('quantProdutoUnidade');
            $table->integer('quantProdutoCaixa');
            $table->decimal('valorProdutoUnidade', 8, 2);
            $table->decimal('valorTotalProduto', 8, 2);
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
