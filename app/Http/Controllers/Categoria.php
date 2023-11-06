<?php

namespace App\Http\Controllers;

use App\Models\Categoria as ModelsCategoria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class Categoria extends Controller
{
    private $moduloCategoria = "admin.categoria";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("productos.categoria",compact("modulos"));
    }
    public function all()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        return DataTables::of(ModelsCategoria::all())->toJson();
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $datos = $request->all();
        $datos['estado'] = $request->has('activo') ? 1 : 0;
        ModelsCategoria::create($datos);
        return response()->json(['success' => 'categoria agregada correctamente']);
    }
    public function update(ModelsCategoria $categoria,Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $datos = $request->all();
        $datos['estado'] = $request->has('activo') ? 1 : 0;
        $categoria->update($datos);
        return response()->json(['success' => 'categoria actualizada correctamente']);
    }
    public function show(ModelsCategoria $categoria)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        $categoria->makeHidden("fechaCreada","fechaActualizada");
        return response()->json(['success' => $categoria]);
    }
    public function destroy(ModelsCategoria $categoria)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCategoria);
        if(isset($verif['session'])){
            return response()->json($verif);
        }
        if($categoria->productos->count() > 0){
            return response()->json(['alerta' => 'No se puede eliminar esta categoría debido a que está asociada a uno o más productos']);
        }
        $categoria->delete();
        return response()->json(['success' => 'categoria eliminada correctamente']);
    }
}
