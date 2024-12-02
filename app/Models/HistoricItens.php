<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricItens extends Model
{
    protected $table = 'historic_itens';

    protected $fillable = [
        'idStatus',
        'idItem',
    ];
}
