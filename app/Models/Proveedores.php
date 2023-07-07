<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    protected $table = 'proveedores';
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    protected $fillable = ['tipo_documento','nro_documento','nombre_proveedor','telefono','celular','direccion','correo','estado'];

    public function contactos()
    {
        return $this->hasMany(ProveedoresContactos::class,'proveedoresFk');
    }
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class,'tipo_documento');
    }
    public function kardex()
    {
        return $this->hasOne(KardexProveedor::class,'id_proveedores');
    }
    // public function compras()
    // {
    //     return $this->hasMany(Compras::class, 'proveedorFk');
    // }
}
