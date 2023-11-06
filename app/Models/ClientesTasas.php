<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesTasas extends Model
{
    public $table = "clientes_tasas";
    protected $fillable = ['id_cliente','id_categoria','tasa'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    
}
