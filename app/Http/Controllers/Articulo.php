<?php

namespace App\Http\Controllers;
use App\Models\Articulo as ModelsArticulo;
use App\Models\Familia;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Articulo extends Controller
{
    private $moduloArticulo = "admin.articulo.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $familias = Familia::where('estado',1)->get();
        return view("productos.articulo",compact("modulos","familias"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca = ModelsArticulo::all();
        return DataTables::of($marca)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        ModelsArticulo::create(['nombreMarca' => $request->nombreMarca, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'marca agregada correctamente']);
    }
    public function show(ModelsArticulo $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca = $marca->makeHidden("fechaCreada","fechaActualizada")->toArray();
        return response()->json(["success" => $marca]);
    }
    public function update(ModelsArticulo $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $marca->update(['nombreMarca' => $request->nombreMarca, 'estado' => $request->has("activo") ? 1 : 0]);
        return response()->json(['success' => 'marca modificada correctamente']);
    }
    public function obtenerSubfamilias(Familia $familia, Request $request){
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        return response()->json(['success' => $familia->subFamila()->select("id","codigo","nombre")->where('estado',1)->get()]);
    }
    public function destroy(ModelsArticulo $marca, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if($marca->productos()->count() > 0){
            return ["alerta" => "Debes eliminar primero los productos relacionados a esta marca"];
        }
        $marca->delete();
        return response()->json(['success' => 'marca eliminada correctamente']);
    }
}
