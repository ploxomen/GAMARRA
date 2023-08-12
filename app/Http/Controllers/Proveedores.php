<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Proveedores as ModelsProveedores;
use App\Models\ProveedoresContactos;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Proveedores extends Controller
{
    private $moduloProveedor = "admin.compras.proveedores";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $modulos = $this->usuarioController->obtenerModulos();
        return view("compras.proveedor",compact("modulos","tiposDocumentos"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $proveedores = ModelsProveedores::with("tipoDocumento:id,documento")->select("id","tipo_documento","nro_documento","nombre_proveedor","telefono","celular","correo","estado")->get();
        return DataTables::of($proveedores)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(!empty($request->nro_documento)){
            $consultaProveedor = ModelsProveedores::where(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento]);
            $consultaProveedor = $request->has("idProveedor") ? $consultaProveedor->where('id','!=',$request->idProveedor)->first() : $consultaProveedor->first();
            if(!empty($consultaProveedor)){
                $tipoDocumento = TipoDocumento::find($request->tipo_documento);
                $tipoDocumento = empty($tipoDocumento) ? 'No definido' : $tipoDocumento->documento;
                return response()->json(['alerta' => 'No se puede registrar el tipo de documento <b>' . $tipoDocumento  . '</b> con el número <b>' . $request->nro_documento . '</b> porque ya se encuentra asociado a <b>' . $consultaProveedor->nombre_proveedor .'</b>']);
            }
        }
        $datosProveedores = $request->only("tipo_documento","nro_documento","nombre_proveedor","celular","telefono","correo","direccion");
        $datosProveedores['estado'] = $request->has("estado") ? 1 : 0;
        $proveedor = $request->has("idProveedor") ? ModelsProveedores::find($request->idProveedor) : ModelsProveedores::create($datosProveedores);
        $contactos = [];
        $resultado = ['success' => 'proveedor agregado correctamente'];
        if($request->has("nombres_c")){
            for ($i=0; $i < count($request->nombres_c); $i++) {
                $datos = [
                    'nombres_completos' => $request->nombres_c[$i],
                    'correo' => isset($request->correo_c[$i]) ? $request->correo_c[$i] : null ,
                    'celular' => isset($request->celular_c[$i]) ? $request->celular_c[$i] : null ,
                    'telefono' => isset($request->telefono_c[$i]) ? $request->telefono_c[$i] : null 
                ];
                if(isset($request->id_c[$i]) && !is_null($request->id_c[$i])){
                    ProveedoresContactos::find($request->id_c[$i])->update($datos);
                }else{
                    $contactos[] = new ProveedoresContactos($datos);
                }
            }
        }
        if($request->has("idProveedor")){
            $proveedor->update($datosProveedores);
            $resultado['success'] = 'proveedor actualizado correctamente';
        }
        if(count($contactos) > 0){
            $proveedor->contactos()->saveMany($contactos);
        }
        return response()->json($resultado);
    }
    public function eliminarContacto(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        ProveedoresContactos::where(['proveedoresFk' => $request->proveedor,'id' => $request->contacto])->delete();
        return response()->json(['success' => 'contacto eliminado correctamente']);
    }
    public function show(ModelsProveedores $proveedor,Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $proveedor = $proveedor->with("contactos")->where(['id' => $proveedor->id])->first();
        return response()->json(['proveedor' => $proveedor]);
    }
    public function destroy($proveedor, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedor);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(ModelsProveedores::find($proveedor)->detalleFardo()->count() > 0){
            return response()->json(['alerta' => 'Este proveedor no puede ser eliminado debido a que está asociado con uno o varios kardex']); 
        }
        ProveedoresContactos::where(['proveedoresFk' => $proveedor])->delete();
        ModelsProveedores::find($proveedor)->delete();
        return response()->json(['success' => 'proveedor eliminado correctamente']);
    }
}
