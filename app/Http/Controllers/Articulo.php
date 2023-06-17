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
        $articulos = ModelsArticulo::obtenerArticulos();
        return DataTables::of($articulos)->toJson();
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
        if(ModelsArticulo::cantidadArituloCodigo($request->codigoArticulo)){
            return response()->json(['alerta' => 'El código ' . $request->codigoArticulo . ' del artículo ya se encuentra registrado, por favor establesca otro código']);
        }
        ModelsArticulo::create(['id_familia_sub' => $request->id_familia_sub, 'estado' => 1,'codigo' => $request->codigoArticulo,'nombre' => $request->nombreArticulo]);
        return response()->json(['success' => 'articulo creado correctamente']);
    }
    public function show($articulo, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $articuloLista = ModelsArticulo::obtenerArticulos($articulo);
        return response()->json(["success" => $articuloLista]);
    }
    public function update($articulo, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(ModelsArticulo::cantidadArituloCodigoEditar($request->codigoArticulo,$articulo)){
            return response()->json(['alerta' => 'El código ' . $request->codigoArticulo . ' del artículo ya se encuentra registrado, por favor establesca otro código']);
        }
        ModelsArticulo::find($articulo)->update(['id_familia_sub' => $request->id_familia_sub, 'estado' => $request->has("estado") ? 1 : 0,'codigo' => $request->codigoArticulo,'nombre' => $request->nombreArticulo]);
        return response()->json(['success' => 'Artículo modificado correctamente']);
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
    public function destroy(ModelsArticulo $articulo, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloArticulo);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $articulo->delete();
        return response()->json(['success' => 'artículo eliminado correctamente']);
    }
}
