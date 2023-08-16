<?php

namespace App\Http\Controllers;

use App\Exports\RankingAduaneros;
use App\Exports\RankingCliente;
use App\Exports\RankingProveedores;
use App\Models\KardexCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Ranking extends Controller
{
    private $moduloCliente = 'admin.ranking.clientes';
    private $moduloProveedores = 'admin.ranking.proveedores';
    private $moduloAduaneros = 'admin.ranking.aduaneros';
    private $usuarioController;
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    function indexProveedores() : View | Redirect  {
        
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return redirect()->route("home");
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ranking.proveedores",compact("modulos"));
    }
    function indexClientes() : View | Redirect  {
        
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home");
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ranking.clientes",compact("modulos"));
    }
    function indexAduaneros() : View | Redirect  {
        
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduaneros);
        if(isset($verif['session'])){
            return redirect()->route("home");
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("ranking.aduaneros",compact("modulos"));
    }
    function listarClientes(Request $request) : Object {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of(KardexCliente::kilajesRankingCliente($request->fechaInicio,$request->fechaFin))->toJson();
    }
    function listarClientesExcel(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::kilajesRankingCliente($request->fechaInicio,$request->fechaFin);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        return Excel::download(new RankingCliente($datos,$fechaFin,$fechaInicio), 'ranking_clientes.xlsx');
    }
    function listarProveedoresExcel(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::cantidadesRankingProveedor($request->fechaInicio,$request->fechaFin);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        return Excel::download(new RankingProveedores($datos,$fechaFin,$fechaInicio), 'ranking_proveedor.xlsx');
    }
    function listarAduanerosExcel(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::precioRankingAduanero($request->fechaInicio,$request->fechaFin);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        return Excel::download(new RankingAduaneros($datos,$fechaFin,$fechaInicio), 'ranking_aduanero.xlsx');
    }
    function listarProveedores(Request $request) : Object {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of(KardexCliente::cantidadesRankingProveedor($request->fechaInicio,$request->fechaFin))->toJson();
    }
    function precioRankingAduanero(Request $request) : Object {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduaneros);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of(KardexCliente::precioRankingAduanero($request->fechaInicio,$request->fechaFin))->toJson();
    }

}
