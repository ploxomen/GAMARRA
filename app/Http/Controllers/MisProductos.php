<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Usuario;
use App\Models\Familia;
use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MisProductos extends Controller
{
    private $moduloProducto = "admin.producto.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $familias = Familia::all()->where('estado',1);
        return view("productos.productos",compact("modulos","familias"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $productos = Productos::obtenerProductos();
        return DataTables::of($productos)->toJson();
    }
    public function obtenerSubfamilias(Familia $familia, Request $request){
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        return response()->json(['success' => $familia->subFamila()->select("id","codigo","nombre")->where('estado',1)->get()]);
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            $datos = $request->all();
            if($request->hasFile('urlImagen')){
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $datos['urlImagen'];
            }
            $datos['estado'] = 1;
            Productos::create($datos);
            DB::commit();
            return response()->json(['success' => 'producto agregado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function show(Productos $producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $producto->urlProductos = !empty($producto->urlImagen) ? route("urlImagen",["productos",$producto->urlImagen]) : null;
        return response()->json(['producto' => $producto->makeHidden("fechaCreada","fechaActualizada")]);
    }
    public function update(Productos $producto, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            $datos = $request->all();
            if($request->hasFile('urlImagen')){
                if(Storage::disk('productos')->exists($producto->urlImagen)){
                    Storage::disk('productos')->delete($producto->urlImagen);
                }
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $datos['urlImagen'];
            }
            $datos['estado'] = $request->has('estado');
            $datos['igv'] = $request->has('igv');
            $producto->update($datos);
            DB::commit();
            return response()->json(['success' => 'producto actualizado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroy(Productos $producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if(Storage::disk('productos')->exists($producto->urlImagen)){
                Storage::disk('productos')->delete($producto->urlImagen);
            }
            $producto->delete();
            DB::commit();
            return response()->json(['success' => 'producto eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function guardarArhivo($request,$key,$disk)
    {
        $nombreOriginal = $request->file($key)->getClientOriginalName();
        $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
        $extension = $request->file($key)->getClientOriginalExtension();
        $archivoNombreAlmacenamiento = $nombreArchivo.'_'.time().'.'.$extension;
        $request->file($key)->storeAs($disk,$archivoNombreAlmacenamiento);
        return $archivoNombreAlmacenamiento;
    }
}
