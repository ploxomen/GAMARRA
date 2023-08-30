<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aduanero extends Model
{
    protected $table = 'aduaneros';
    protected $fillable = ['id_pais','tipo_documento','nro_documento','nombre_completo','tasa','principal','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function scopeObtenerDatos($query,$ordenarNombre = false) {
        $aduaneros = $query->select("aduaneros.id","aduaneros.nombre_completo","td.documento","aduaneros.nro_documento","aduaneros.tasa","aduaneros.estado","aduaneros.principal","p.pais_espanish")
        ->leftJoin("tipo_documento AS td","aduaneros.tipo_documento","=","td.id")
        ->join("paises AS p","aduaneros.id_pais","=","p.id");
        return $ordenarNombre ? $aduaneros->orderBy("aduaneros.nombre_completo")->get() : $aduaneros->get();
    }
}
