<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexClienteCategoria extends Model
{
    protected $table = 'kardex_cliente_categoria';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['id_kardex_cliente','id_categoria','tasa'];

    public static function obtenerTasas($idCliente,$idKardex) {
        return KardexCliente::select("kardex_cliente_categoria.id","categorias.nombreCategoria","kardex_cliente_categoria.tasa")
        ->join('kardex_cliente_categoria','kardex_cliente_categoria.id_kardex_cliente','=','kardex_cliente.id')
        ->join("categorias","categorias.id","=","kardex_cliente_categoria.id_categoria")
        ->where(['kardex_cliente.id_kardex' => $idKardex,'kardex_cliente.id_cliente' => $idCliente])->groupBy("kardex_cliente_categoria.id_categoria")->get();
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class,'id_categoria');
    }
}
