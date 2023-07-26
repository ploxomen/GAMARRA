<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KardexProveedor extends Model
{
    public $table = "kardex_proveedores";
    protected $fillable = ['id_kardex','id_proveedores','observaciones','fechaRecepcion','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function scopeObtenerProveedoresKardexs($query){
        return $query->select("clientes.nombreCliente","proveedores.nombre_proveedor","kardex_fardos.id_kardex AS idKardex","kardex_proveedores.id","kardex_proveedores.estado","kardex_proveedores.id_proveedores AS idProveedor")
        ->selectRaw("LPAD(kardex_fardos.id_kardex,5,'0') AS nroKardex,LPAD(kardex_proveedores.id,5,'0') AS nroKardexProveedor")
        ->join('kardex_fardos','kardex_fardos.id_kardex','=','kardex_proveedores.id_kardex')
        ->join('proveedores','proveedores.id','=','kardex_proveedores.id_proveedores')
        ->join('clientes','clientes.id','=','kardex_fardos.id_cliente')
        ->where('kardex_fardos.estado','>=',1)->where('kardex_proveedores.estado',1)
        ->groupBy("kardex_fardos.id_kardex")
        ->groupBy("kardex_proveedores.id_proveedores")
        ->get();
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class,'id_proveedores');
    }
}
