<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexFardoDetalle extends Model
{
    public $table = "kardex_fardos_detalle";
    public $timestamps = false;
    protected $fillable = ['id_fardo','cantidad','precio','id_proveedor','id_producto','id_presentacion','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function scopeObtenerProveedoresKardex($query,$idKardex){
        return $query->select("kardex_fardos_detalle.*")
        ->join('kardex_fardos','kardex_fardos.id','=','kardex_fardos_detalle.id_fardo')
        ->join('kardex','kardex.id','=','kardex_fardos.id_kardex')
        ->where('kardex.id',$idKardex)
        ->groupBy("kardex_fardos_detalle.id_proveedor")->get();
    }
    public function scopeObtenerProductos($query,$idProveedor,$listaFardos){
        return $query->select("kardex_fardos_detalle.id","kardex_fardos_detalle.cantidad","kardex_fardos_detalle.precio","productos.nombreProducto","presentacion.presentacion")
        ->join('productos','productos.id','=','kardex_fardos_detalle.id_producto')
        ->join('presentacion','presentacion.id','=','kardex_fardos_detalle.id_presentacion')
        ->where('kardex_fardos_detalle.id_proveedor' ,$idProveedor)->whereIn('kardex_fardos_detalle.id_fardo',$listaFardos)->where('kardex_fardos_detalle.estado','!=',0)->get();
    }
    public function scopeObtenerProductosPreFactura($query,$idKardex){
        return $query->select("kardex_fardos_detalle.*")
        ->selectRaw("sum(kardex_fardos_detalle.cantidad) AS totalCantidades")
        ->join('kardex_fardos','kardex_fardos.id','=','kardex_fardos_detalle.id_fardo')
        ->join('kardex','kardex.id','=','kardex_fardos.id_kardex')
        ->where('kardex.id',$idKardex)
        ->groupBy("kardex_fardos_detalle.id_producto")->get();    
    }
    public function productos()
    {
        return $this->belongsTo(Productos::class,'id_producto');
    }
    public function presentaciones()
    {
        return $this->belongsTo(Presentacion::class,'id_presentacion');
    }
}