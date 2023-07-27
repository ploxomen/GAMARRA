<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    public $table = "kardex";
    protected $fillable = ['nroFardoActivo','cantidad','kilaje','importe','estado'];
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
    public static function scopeMisKardex($query) {
        return $query->select("kardex.id","kardex.cantidad","kardex.kilaje","kardex.estado")
        ->selectRaw("LPAD(kardex.id,5,'0') AS nroKardex,DATE_FORMAT(kardex.fechaCreada,'%d/%m/%Y') AS fechaKardex")
        // ->join("kardex_fardos",'kardex.id','=','kardex_fardos.id_kardex')
        ->where('estado','!=',1)->where('estado','!=',0)->get();
    }
    public function fardos()
    {
        return $this->hasMany(KardexFardo::class,'id_kardex');
    }
}
