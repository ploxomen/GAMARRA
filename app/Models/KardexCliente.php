<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KardexCliente extends Model
{
    public $table = "kardex_cliente";
    protected $fillable = ['id_kardex','id_cliente','tasa','tasa_extranjera'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
