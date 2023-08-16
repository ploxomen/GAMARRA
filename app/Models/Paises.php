<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paises extends Model
{
    protected $table = 'paises';
    protected $primaryKey = 'id';
    protected $fillable = ['pais_espanish','pais_english','estado'];
    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Clientes::class,'id_pais');
    }
}
