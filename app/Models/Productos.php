<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Productos extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['nombreProducto','codigo','id_articulo','descripcion','precioVenta','urlImagen','estado'];

    // public function marca()
    // {
    //     return $this->belongsTo(Marca::class,'marcaFk');
    // }
    public function articulo()
    {
        return $this->belongsTo(Articulo::class,'id_articulo');
    }
    public function scopeCantidadProductosCodigo($query,$codigo){
        return $query->where('codigo',$codigo)->count();
    }
    public function scopeCantidadProductosCodigoEditar($query,$codigo,$productoId){
        return $query->where('codigo',$codigo)->where('id','!=',$productoId)->count();
    }
    public function scopeObtenerProductoEditar($query,$productoId){
        $producto = $query->select("productos.id_articulo AS articuloId","productos.codigo AS productoCodigo","productos.nombreProducto AS productoNombre","productos.descripcion AS productoDescripcion","productos.precioVenta AS productoPrecioVenta","productos.urlImagen AS productoImagen","productos.estado AS productoEstado","familia_sub.id AS familiaSubId","familia_sub.id_familia AS familiaId")->join("articulo","productos.id_articulo","=","articulo.id")
        ->join("familia_sub","articulo.id_familia_sub","=","familia_sub.id")
        ->where('productos.id',$productoId)->first();
        if(!empty($producto)){
            $producto->listaFamiliaSub = SubFamilias::where(['id_familia' => $producto->familiaId,'estado' => 1])->get();
            $producto->listaArticulos = Articulo::where(['id_familia_sub' => $producto->familiaSubId,'estado' => 1])->get();
        }
        return $producto;
    }
    public function scopeObtenerProductos($query) {
        return $query->select("articulo.nombre AS articuloNombre","productos.id AS productoId","familia.nombre AS familiaNombre","familia_sub.nombre AS familiaSubNombre","productos.codigo AS productoCodigo","productos.nombreProducto AS productoNombre","productos.precioVenta","productos.estado AS productoEstado")->join("articulo","productos.id_articulo","=","articulo.id")
        ->join("familia_sub","familia_sub.id","=","articulo.id_familia_sub")
        ->join("familia","familia.id","=","familia_sub.id_familia")
        ->get();
    }
    // public function presentacion()
    // {
    //     return $this->belongsTo(Presentacion::class,'presentacionFk');
    // }
    // public function perecederos()
    // {
    //     return $this->hasMany(Perecedero::class,'productoFk');
    // }
    // public function compras()
    // {
    //     return $this->belongsToMany(Compras::class, 'compras_detalle', 'productoFk', 'compraFk')->withTimestamps();
    // }
    // public function cotizacion()
    // {
    //     return $this->belongsToMany(Cotizacion::class, 'cotizacion_detalle', 'productoFk', 'cotizacionFk')->withTimestamps();
    // }
    // public function scopeCantidadMaximaPerecedero($query,$id,$cantidad,$idPerecedero = null)
    // {
    //     $producto = $query->where('id',$id);
    //     if(empty($producto->first())){
    //         return ['error' => 'no se encontro el producto'];
    //     }
    //     $producto = $producto->with("presentacion")->withSum(["perecederos" => function($sub) use($idPerecedero){
    //         $sub->where('estado',1);
    //         if(!empty($idPerecedero)){
    //             $sub->where('id','!=',$idPerecedero);
    //         }
    //     }],"cantidad")->first();
    //     $cantidadMax = intval($producto->perecederos_sum_cantidad) + intval($cantidad);
    //     $cantidadPermitida = $producto->cantidad - intval($producto->perecederos_sum_cantidad);
    //     if($cantidadPermitida <= 0){
    //         return ["error" => "Se superó el limite de cantidad permitida, si desea agregar más perecederos, intente ampliar el stock del producto"];
    //     }
    //     if($cantidadMax > $producto->cantidad){
    //         return ['error' => 'La cantidad máxima para el producto ' . $producto->nombreProducto . ' es de ' . $cantidadPermitida. ' '. $producto->presentacion->siglas .', por favor intente ingresando la cantidad máxima o inferior.'];
    //     }
    //     return ['success' => true];
    // }
    // public function scopeProductosMasVendidos($query,int $limites)
    // {
    //     return DB::table($this->table . ' AS p')->select("p.nombreProducto")
    //     ->selectRaw("SUM(vd.cantidad) AS total")
    //     ->join("ventas_detalle AS vd","vd.productoFk","=","p.id")
    //     ->join("ventas AS v","vd.ventaFk","=","v.id")
    //     ->where(["p.estado"=>1,"v.estado" => 1])
    //     ->whereYear('v.fechaVenta',date('Y'))
    //     ->groupBy("p.id")->orderByRaw("SUM(vd.cantidad) DESC")->limit($limites)->get();
    // }
    // public function scopeProductosPorVencer($query)
    // {
    //     return DB::table($this->table . ' AS p')->select("p.nombreProducto")
    //     ->selectRaw("SUM(pe.cantidad) AS cantidad,DATE_FORMAT(pe.vencimiento,'%d/%m/%Y') AS fechaVencimiento,DATEDIFF(pe.vencimiento,CURDATE()) AS diasPasados")
    //     ->join("perecederos AS pe","pe.productoFk","=","p.id")
    //     ->where(["p.estado"=>1,"pe.estado" => 1])
    //     ->whereRaw("DATE_ADD(CURDATE(), INTERVAL 15 DAY) >= pe.vencimiento")
    //     ->groupByRaw("pe.productoFk,pe.vencimiento")->orderByRaw("DATEDIFF(CURDATE(),pe.vencimiento) ASC")->get();
    // }
}
