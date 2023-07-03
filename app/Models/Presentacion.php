<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    public $table = "presentacion";
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['presentacion','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected function serializeDate($date)
    {
        return $date->format('d/m/Y h:i a');
    }
    public static function obtenerPresentaciones(){
        return Presentacion::where('estado',1)->orderBy("presentacion")->get();
    }
}
