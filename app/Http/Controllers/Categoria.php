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
        return DataTables::of(ModelsCategoria::where('estado',1)->get())->toJson();
    }

}
