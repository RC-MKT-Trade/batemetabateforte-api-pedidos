<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itens extends Model
{
    use SoftDeletes, HasFactory;

    // Nome da tabela (opcional, se o nome seguir a convenção pluralizada)
    protected $table = 'itens';

    // Campos que podem ser atribuídos em massa (mass assignment)
    protected $fillable = [
        'idPedido',
        'chaveSefaz',
        'descCompleta',
        'ean',
        'quantProdutoUnidade',
        'quantProdutoCaixa',
        'valorProdutoUnidade',
        'valorTotalProduto',
    ];

    /**
     * Relacionamento: Um item pertence a um pedido.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedidos::class);
    }
}
