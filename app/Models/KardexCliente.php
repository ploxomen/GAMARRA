<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class KardexCliente extends Model
{
    public $table = "kardex_cliente";
    protected $fillable = ['id_kardex','id_cliente'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function kardexClientesGeneralReporte($fechaInicio,$fechaFin,$cliente) {
        if(empty($cliente)){
            return [];
        }
        $kardexs = KardexCliente::select("c.nombreCliente","k.guia_aerea","c.id AS idCliente","kardex_cliente.id_kardex","kardex_cliente.id")
        ->selectRaw("LPAD(k.id,5,'0') AS nro_kardex,DATE_FORMAT(k.fechaCreada,'%d/%m/%Y') AS fecha_kardex")
        ->join("kardex AS k","kardex_cliente.id_kardex","=","k.id")
        ->join("clientes AS c","c.id","=","kardex_cliente.id_cliente")
        ->where('K.estado','>=',2)
        ->whereRaw("DATE_FORMAT(k.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaInicio,$fechaFin])->groupBy("kardex_cliente.id_kardex","kardex_cliente.id_cliente");
        if($cliente !== 'todos'){
            return $kardexs->where('kardex_cliente.id_cliente',$cliente)->get();
        }
        return $kardexs->get();
    }

    public function kardex()
    {
        return $this->belongsTo(Kardex::class,'id_kardex');
    }
    public static function kardexClientesGeneral($fechaInicio,$fechaFin,$cliente) {
        if(empty($cliente)){
            return [];
        }
        $kardexs = DB::table('kardex_cliente AS kc')->select("kf.nro_fardo","c.nombreCliente","p.nombreProducto","kfd.cantidad")
        ->selectRaw("LPAD(k.id,5,'0') AS nro_kardex,DATE_FORMAT(k.fechaCreada,'%d/%m/%Y') AS fecha_kardex")
        ->join("kardex AS k","kc.id_kardex","=","k.id")
        ->join("clientes AS c","c.id","=","kc.id_cliente")
        ->join("kardex_fardos AS kf",function (JoinClause $join) {
            $join->on("kc.id_kardex","=","kf.id_kardex")->on("kc.id_cliente","=","kf.id_cliente");
        })
        ->join("kardex_fardos_detalle AS kfd","kf.id","=","kfd.id_fardo")
        ->join("productos AS p","p.id","=","kfd.id_producto")
        ->where('K.estado','>=',2)
        ->whereRaw("DATE_FORMAT(k.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaInicio,$fechaFin])->groupBy("k.id","kf.id","p.id");
        if($cliente !== 'todos'){
            return $kardexs->where('kc.id_cliente',$cliente)->get();
        }
        return $kardexs->get();
    }

    static function kilajesRankingCliente($fechaIncio,$fechaFin,$buscador) : Object {
        return DB::table('kardex_fardos AS kf')->select("c.nombreCliente","p.pais_espanish","c.id")
        ->selectRaw("SUM(kf.kilaje) AS kilajes")
        ->join("kardex AS k","k.id","=","kf.id_kardex")
        ->join("clientes AS c","kf.id_cliente","=","c.id")
        ->join("paises AS p","p.id","=","c.id_pais")
        ->where('k.estado','>',1)
        ->where('c.nombreCliente','LIKE','%' . $buscador .'%')
        ->where('kf.estado','>=',2)->whereRaw("DATE_FORMAT(kf.fechaCreada,'%Y-%m-%d') BETWEEN ? AND ?",[$fechaIncio,$fechaFin])->groupBy("kf.id_cliente")->orderBy("kilajes","desc")->get();
    }
    static function cantidadesRankingProveedor($fechaIncio,$fechaFin,$buscador) : Object {
        return DB::table('kardex_fardos_detalle AS kfd')
        ->select("p.nombre_proveedor","p.id","pro.nombreProducto")
        ->selectRaw("SUM(kfd.cantidad) AS cantidades")
        ->join("kardex_fardos AS kf","kfd.id_fardo","=","kf.id")
        ->join("kardex AS k","k.id","=","kf.id_kardex")
        ->join("proveedores AS p","kfd.id_proveedor","=","p.id")
        ->join("productos AS pro","pro.id","=","kfd.id_producto")
        ->where('k.estado','>',1)
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
    public function tasasCategorias()
    {
        return $this->hasMany(KardexClienteCategoria::class,'id_kardex_cliente');
    }
}
