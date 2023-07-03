<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexFardoDetalle extends Model
{
    public $table = "kardex_fardos_detalle";
    public $timestamps = false;
    protected $fillable = ['id_fardo','cantidad','id_proveedor','id_producto','id_presentacion','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
