<?php

namespace App\Http\Controllers;

use App\Models\Aduanero;
use App\Models\Articulo as ModelsArticulo;
use App\Models\Familia;
use App\Models\Paises;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Aduaneros extends Controller
{
    private $moduloAduanero = "admin.aduaneros.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $paises = Paises::where('estado',1)->get();
        return view("ventas.aduanero",compact("modulos","tiposDocumentos","paises"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduaneros = Aduanero::obtenerDatos();
        return DataTables::of($aduaneros)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->only("tipo_documento","nro_documento","nombre_completo","id_pais","tasa");
        if($request->has("principal")){
            Aduanero::where('estado',1)->update(['principal' => 0]);
            $datos['principal'] = 1;
        }
        $datos['estado'] = 1;
        Aduanero::create($datos);
        return response()->json(['success' => 'aduanero creado correctamente']);
    }
    public function show($aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduaneroInfo = Aduanero::find($aduanero);
        return response()->json(["success" => $aduaneroInfo]);
    }
    public function update($aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        Aduanero::find($aduanero)->where('estado',1)->update(['principal' => 0]);
        $datos = $request->only("tipo_documento","nro_documento","nombre_completo","id_pais","tasa");
        if($request->has("principal")){
            Aduanero::where('estado',1)->update(['principal' => 0]);
            $datos['principal'] = 1;
        }
        $datos['estado'] = $request->has("estado") ? 1 : 0;
        Aduanero::find($aduanero)->update($datos);
        return response()->json(['success' => 'aduanero modificado correctamente']);
    }
    
    public function destroy(Aduanero $aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduanero->delete();
        return response()->json(['success' => 'aduanero eliminado correctamente']);
    }
}
