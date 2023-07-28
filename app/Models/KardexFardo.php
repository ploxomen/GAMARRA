<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KardexFardo extends Model
{
    public $table = "kardex_fardos";
    protected $fillable = ['id_kardex','id_cliente','nro_fardo','cantidad','kilaje','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function productosDetalle()
    {
        return $this->hasMany(KardexFardoDetalle::class,'id_fardo');
    }
    public function clientes()
    {
        return $this->belongsTo(Clientes::class,'id_cliente');
    }
}
