<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KardexProveedor extends Model
{
    public $table = "kardex_proveedores";
    protected $fillable = ['id_kardex','id_proveedores','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function scopeObtenerProveedoresKardexs($query){
        return $query->select("clientes.nombreCliente","proveedores.nombre_proveedor","kardex.id AS idKardex","kardex_proveedores.id","kardex_proveedores.estado","kardex_proveedores.id_proveedores AS idProveedor")
        ->selectRaw("LPAD(kardex.id,5,'0') AS nroKardex,LPAD(kardex_proveedores.id,5,'0') AS nroKardexProveedor")
        ->join('kardex','kardex.id','=','kardex_proveedores.id_kardex')
        ->join('proveedores','proveedores.id','=','kardex_proveedores.id_proveedores')
        ->join('clientes','clientes.id','=','kardex.id_cliente')
        ->where('kardex.estado','>',1)->where('kardex_proveedores.estado',1)
        ->get();
    }
}
