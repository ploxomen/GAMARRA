<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubFamilias extends Model
{
    protected $table = 'familia_sub';
    protected $primaryKey = 'id';
    protected $fillable = ['id_familia','codigo','nombre','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';

    public function familia()
    {
        return $this->belongsTo(Familia::class,'id_familia');
    }

    function scopeCanitdadSubFamilias($query,$codigo){
        return $query->where('codigo',$codigo)->count();
    }
    function scopeCanitdadSubFamiliasEditar($query,$codigo,$id){
        return $query->where('codigo',$codigo)->where('id','!=',$id)->count();
    }
}
