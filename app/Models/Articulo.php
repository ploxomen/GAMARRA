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
}
