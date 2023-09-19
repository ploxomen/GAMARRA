<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    public $table = "kardex";
    protected $fillable = ['nroFardoActivo','id_aduanero','tasa_extranjera','cantidad','kilaje','importe','estado','guia_remision_sunat','factura_sunat','factura_total_sunat'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function verKardexPorTerminar($idCliente,$idKardex = null) {
        $condicionKardex = !is_null($idKardex) ? ['id' => $idKardex] : ['estado' => 1];
        $kardex = Kardex::where($condicionKardex)->first();
        if(!empty($kardex)){
            $kardex->update(['nroFardoActivo' => null]);
            $kardex->proveedores = Proveedores::where('estado',1)->get();
            $kardex->presentaciones = Presentacion::obtenerPresentaciones();
            $kardex->productos = Productos::where('estado',1)->get();
            $kardex->listaFardos = KardexFardo::where(['id_kardex' => $kardex->id,'id_cliente' => $idCliente])->where('estado',!is_null($idKardex) ? '>=' : '=',1)->get();
            foreach ($kardex->listaFardos as $fardo) {
                $fardo->productos = KardexFardoDetalle::where('id_fardo',$fardo->id)->where('estado',!is_null($idKardex) ? '>=' : '=',1)->get();
            }
        }
        return $kardex;
    }
    public function scopeFacturacionesElectronicas($query){
        return $query->select("kardex.id","a.nombre_completo","f.estado")
        ->selectRaw("LPAD(kardex.id,5,'0') AS nroKardex,IF(f.serie IS NULL,'No establecido',CONCAT(f.serie,'-',f.numero)) AS numeroFactura,CONCAT('$', kardex.importe) AS montoSistema,IF(f.monto_total IS NULL,'No establecido',CONCAT('$ ',f.monto_total)) AS montoFactura,IF(f.fecha_emision IS NULL,'No establecido',DATE_FORMAT(f.fecha_emision,'%d/%m/%Y')) AS fechaFactura")
        ->join("aduaneros AS a","a.id","=","kardex.id_aduanero")
        ->leftJoin("facturas AS f","f.id_kardex","=","kardex.id")
        ->where('kardex.estado','>',1)->get();
    }
    public static function scopeMisKardex($query) {
        return $query->select("kardex.id","kardex.cantidad","kardex.tasa_extranjera","kardex.guia_remision_sunat","kardex.factura_sunat","kardex.kilaje","kardex.estado","kardex.id_aduanero","kardex.importe")
        ->selectRaw("LPAD(kardex.id,5,'0') AS nroKardex,DATE_FORMAT(kardex.fechaCreada,'%d/%m/%Y') AS fechaKardex")
        ->where('estado','!=',0)->get();
    }
    public function fardos()
    {
        return $this->hasMany(KardexFardo::class,'id_kardex');
    }
    public function aduanero()
    {
        return $this->belongsTo(Aduanero::class,'id_aduanero');
    }
}
