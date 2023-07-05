<?php

namespace App\Http\Controllers;

use App\Models\KardexProveedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class KardexProveedores extends Controller
{
    private $usuarioController;
    private $moduloMisKardexProveedor = "admin.proveedores.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("kardex.misKardexProveedores",compact("modulos"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $proveedores = KardexProveedor::obtenerProveedoresKardexs();
        return DataTables::of($proveedores)->toJson();
    }
    public function verGuiaReporte(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $pdf = Pdf::loadView('kardex.reportesPdf.guiaRecepcion');
        return $pdf->stream("Guía de Recepción.pdf");
    }
}
