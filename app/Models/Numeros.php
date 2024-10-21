<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Numeros extends Model
{
    protected $table = 'numeros';


    protected $fillable = [
        'serial',
        'isActive',
        'idPedido',

    ];
}
