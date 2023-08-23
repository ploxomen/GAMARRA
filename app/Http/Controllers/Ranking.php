<?php

namespace App\Http\Controllers;

use App\Exports\RankingAduaneros;
use App\Exports\RankingCliente;
use App\Exports\RankingProveedores;
use App\Models\KardexCliente;
use Barryvdh\DomPDF\Facade\Pdf;
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
        return DataTables::of(KardexCliente::kilajesRankingCliente($request->fechaInicio,$request->fechaFin,''))->toJson();
    }
    function listarClientesExcel(Request $request,$tipo) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::kilajesRankingCliente($request->fechaInicio,$request->fechaFin,$request->buscador);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        switch ($tipo) {
            case 'excel':
                return Excel::download(new RankingCliente($datos,$fechaFin,$fechaInicio), 'ranking_clientes.xlsx');
            break;
            case 'pdf':
                return Pdf::loadView('ranking.export.clientesPdf',compact("fechaInicio","fechaFin","datos"))->stream('ranking_clientes.pdf');
            break;
            default:
                return abort(404);
            break;
        }
    }
    function listarProveedoresExcel(Request $request,$tipo) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::cantidadesRankingProveedor($request->fechaInicio,$request->fechaFin,$request->buscador);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        switch ($tipo) {
            case 'excel':
                return Excel::download(new RankingProveedores($datos,$fechaFin,$fechaInicio), 'ranking_proveedor.xlsx');
            break;
            case 'pdf':
                return Pdf::loadView('ranking.export.proveedoresPdf',compact("fechaInicio","fechaFin","datos"))->stream('ranking_proveedores.pdf');
            break;
            default:
                return abort(404);
            break;
        }
    }
    function listarAduanerosExcel(Request $request,$tipo) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $datos = KardexCliente::precioRankingAduanero($request->fechaInicio,$request->fechaFin,$request->buscador);
        $fechaInicio = date('d/m/Y',strtotime($request->fechaInicio));
        $fechaFin = date('d/m/Y',strtotime($request->fechaFin));
        switch ($tipo) {
            case 'excel':
                return Excel::download(new RankingAduaneros($datos,$fechaFin,$fechaInicio), 'ranking_aduanero.xlsx');
            break;
            case 'pdf':
                return Pdf::loadView('ranking.export.aduanerosPdf',compact("fechaInicio","fechaFin","datos"))->stream('ranking_aduaneros.pdf');
            break;
            default:
                return abort(404);
            break;
        }
    }
    function listarProveedores(Request $request) : Object {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProveedores);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of(KardexCliente::cantidadesRankingProveedor($request->fechaInicio,$request->fechaFin,''))->toJson();
    }
    function precioRankingAduanero(Request $request) : Object {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduaneros);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return DataTables::of(KardexCliente::precioRankingAduanero($request->fechaInicio,$request->fechaFin,''))->toJson();
    }

}
