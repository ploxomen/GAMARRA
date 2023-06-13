<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedoresContactos extends Model
{
    protected $table = 'proveedores_contactos';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['proveedoresFk','tipo_documento','nro_documento','cargo','nombres_completos','correo','celular','telefono'];
    public function proveedores()
    {
        return $this->belongsTo(Proveedores::class,'proveedoresFk');
    }
}
