<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;


    protected $table = 'pedidos';


    protected $fillable = [
        'statusCompra',
        'codCliente',
        'cnpjCliente',
        'cnpjFilialBf',
        'codRca',
        'numPedido',
        'dataPedido',
        'valorTotalPedido',
        'canalVenda',
        'segmento',
        'criadoEm',
        'atualizadoEm',
    ];

    /**
     * Relacionamento: Um pedido tem muitos itens.
     */

    public function itens()
    {
        return $this->hasMany(Itens::class);
    }
}
