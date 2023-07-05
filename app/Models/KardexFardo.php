<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexFardo extends Model
{
    public $table = "kardex_fardos";
    protected $fillable = ['id_kardex','nro_fardo','cantidad','kilaje','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
}
