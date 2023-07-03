<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    public $table = "kardex";
    protected $fillable = ['id_cliente','nroFardoActivo','cantidad','kilaje','importe','total','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public static function verKardexPorTerminar($idCliente) {
        $kardex = Kardex::where(['id_cliente'=>$idCliente,'estado' => 1])->first();
        if(!empty($kardex)){
            $kardex->proveedores = Proveedores::where('estado',1)->get();
            $kardex->presentaciones = Presentacion::obtenerPresentaciones();
            $kardex->productos = Productos::where('estado',1)->get();
            $kardex->listaFardos = KardexFardo::where(['id_kardex' => $kardex->id,'estado' => 1])->get();
            foreach ($kardex->listaFardos as $fardo) {
                $fardo->productos = KardexFardoDetalle::where(['id_fardo'=>$fardo->id,'estado' => 1])->get();
            }
        }
        return $kardex;
    }
}
