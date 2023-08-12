<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Clientes extends Model
{
    public $table = "clientes";
    protected $fillable = ['nombreCliente','id_pais','tipo_documento','nro_documento','tasa','id_usuario','estado'];
    const CREATED_AT = 'fechaCreada';
    const UPDATED_AT = 'fechaActualizada';
    public function usuario()
    {
        return $this->belongsTo(User::class,'id_usuario');
    }
    public function pais()
    {
        return $this->hasOne(Paises::class,'id_pais');
    }
    public function scopeObenerClientes($query)
    {
        return $query->select("clientes.id","tipo_documento.documento","paises.pais_espanish","clientes.nro_documento","usuarios.correo","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("paises","paises.id","=","clientes.id_pais")
        ->join("tipo_documento","clientes.tipo_documento","=","tipo_documento.id","left")->get();
    }
    public function scopeObenerClientesActivos($query)
    {
        return $query->select("clientes.id","tipo_documento.documento","usuarios.nroDocumento","usuarios.correo","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("tipo_documento","usuarios.tipoDocumento","=","tipo_documento.id","left")->where('clientes.estado',1)->get();
    }
    public function scopeObenerCliente($query,$idCliente)
    {
        $cliente = $query->select("clientes.id","clientes.tasa","usuarios.correo","clientes.id_pais AS paises","clientes.tipo_documento","clientes.nro_documento","clientes.nombreCliente","usuarios.celular","usuarios.telefono","usuarios.direccion","clientes.estado")
        ->join("usuarios","usuarios.id","=",'clientes.id_usuario')
        ->join("tipo_documento","clientes.tipo_documento","=","tipo_documento.id","left")
        ->where(['clientes.id' => $idCliente])->first();
        if(!empty($cliente)){
            $cliente->contactos = DB::table('clientes_contactos')->select("id","nombreContacto","numeroContacto")->where('idCliente',$cliente->id)->get();
        }
        return $cliente;
    }
    public function scopeVerificarCorreo($query,$idCliente,$correo)
    {
        $cliente = $this->obtenerIdUsuario($idCliente); 
        return DB::table("usuarios")->where(['correo' => $correo])->where('id','!=',$cliente->id_usuario)->count();
    }
    public function obtenerIdUsuario($idCliente)
    {
        return DB::table($this->table)->where('id',$idCliente)->first();
    }
    public function contactos()
    {
        return $this->hasMany(ClientesContactos::class,'idCliente');
    }
    public function fardos()
    {
        return $this->hasMany(KardexFardo::class,'id_cliente');
    }
    
    
}
