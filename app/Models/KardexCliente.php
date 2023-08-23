<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KardexCliente extends Model
{
    public $table = "kardex_cliente";
    protected $fillable = ['id_kardex','id_cliente','tasa','tasa_extranjera'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    static function kilajesRankingCliente($fechaIncio,$fechaFin,$buscador) : Object {
        return DB::table('kardex_fardos AS kf')->select("c.nombreCliente","p.pais_espanish","c.id")
        ->selectRaw("SUM(kf.kilaje) AS kilajes")
        ->join("clientes AS c","kf.id_cliente","=","c.id")
        ->join("paises AS p","p.id","=","c.id_pais")
        ->where('c.nombreCliente','LIKE','%' . $buscador .'%')
        ->where('kf.estado','>=',2)->whereRaw("DATE_FORMAT(kf.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaIncio,$fechaFin])->groupBy("kf.id_cliente")->orderBy("kilajes","desc")->get();
    }
    static function cantidadesRankingProveedor($fechaIncio,$fechaFin,$buscador) : Object {
        return DB::table('kardex_fardos_detalle AS kfd')
        ->select("p.nombre_proveedor","p.id","pro.nombreProducto")
        ->selectRaw("SUM(kfd.cantidad) AS cantidades")
        ->join("kardex_fardos AS kf","kfd.id_fardo","=","kf.id")
        ->join("proveedores AS p","kfd.id_proveedor","=","p.id")
        ->join("productos AS pro","pro.id","=","kfd.id_producto")
        ->where('p.nombre_proveedor','LIKE','%' . $buscador .'%')
        ->where('kfd.estado','>=',2)->whereRaw("DATE_FORMAT(kf.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaIncio,$fechaFin])
        ->groupBy("kfd.id_proveedor")->groupBy("kfd.id_producto")->orderBy("cantidades","desc")->get();
    }
    static function precioRankingAduanero($fechaIncio,$fechaFin,$buscador) : Object {
        
        return DB::table('kardex AS k')->selectRaw("DISTINCT kf.id")->select("a.nombre_completo","p.pais_espanish","a.id","kf.id AS sss","kf.kilaje")
        ->selectRaw("(SUM(kf.kilaje) * k.tasa_extranjera) AS costos")
        ->join("kardex_fardos AS kf","kf.id_kardex","=","k.id")
        ->join("aduaneros AS a","k.id_aduanero","=","a.id")
        ->join("paises AS p","p.id","=","a.id_pais")
        ->where('a.nombre_completo','LIKE','%' . $buscador .'%')
        ->where('k.estado','>=',2)->where('a.estado','!=',0)
        ->whereRaw("DATE_FORMAT(k.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaIncio,$fechaFin])
        ->groupBy("a.id")->orderBy("costos","desc")
        ->get();
    }
}
