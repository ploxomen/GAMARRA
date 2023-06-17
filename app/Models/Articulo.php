<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulo';
    protected $primaryKey = 'id';
    protected $fillable = ['id_familia_sub','codigo','nombre','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function scopeCantidadArituloCodigo($query,$codigo){
        return $query->where('codigo',$codigo)->count();
    }
    public function scopeCantidadArituloCodigoEditar($query,$codigo,$idArticulo){
        return $query->where('codigo',$codigo)->where('id','!=',$idArticulo)->count();
    }
    public function scopeObtenerArticulos($query,$idArticulo = null){
        $articulos = $query->select("articulo.id AS articuloId","articulo.id_familia_sub AS familiaSubId","familia_sub.nombre AS familiSubNombre","familia.id AS familiaId","familia.nombre AS familaNombre","articulo.nombre AS articuloNombre","articulo.codigo AS articuloCodigo","articulo.estado AS articuloEstado")->join("familia_sub","articulo.id_familia_sub","=","familia_sub.id")
        ->join("familia","familia.id","=","familia_sub.id_familia");
        if(!empty($idArticulo)){
            $articulo = $articulos->where('articulo.id',$idArticulo)->first();
            $articulo->listaFamiliaSub = SubFamilias::where(['id_familia' => $articulo->familiaId,'estado' => 1])->get();
            return $articulo;
        }
        return $articulos->get();
    }
}
