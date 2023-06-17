<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    protected $table = 'familia';
    protected $primaryKey = 'id';
    protected $fillable = ['codigo','nombre','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected function serializeDate($date)
    {
        return $date->format('d/m/Y h:i a');
    }
    public function subFamila()
    {
        return $this->hasMany(SubFamilias::class,'id_familia');
    }
    public function productos()
    {
        return $this->hasMany(Productos::class,'id_familia');
    }
    function scopeCanitdadFamilias($query,$codigo){
        return $query->where('codigo',$codigo)->count();
    }
    function scopeCanitdadFamiliasEditar($query,$codigo,$id){
        return $query->where('codigo',$codigo)->where('id','!=',$id)->count();
    }
}
