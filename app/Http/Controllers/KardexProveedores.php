<?php

namespace App\Http\Controllers;

use App\Models\KardexFardo;
use App\Models\KardexFardoDetalle;
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
    public function verGuiaReporte($kardex,$proveedor)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $kardex = KardexProveedor::where(['id_kardex' => $kardex,'id_proveedores' => $proveedor])->where('estado','!=',0)->first();
        if(empty($kardex)){
            return redirect()->route("home");
        }
        $listaFardos = KardexFardo::select("id")->where('id_kardex',$kardex->id_kardex)->where('estado','!=',0)->get()->toArray();
        if(empty($kardex)){
            return redirect()->route("home");
        }
        $listaFardo = array_map(function($v){
            return $v['id'];
        },$listaFardos);
        $listaDetalles = KardexFardoDetalle::obtenerProductos($kardex->id_proveedores,$listaFardo);
        $fechaLarga = strtoupper($this->obtenerFechasLargas($kardex->fechaRecepcion));
        $pdf = Pdf::loadView('kardex.reportesPdf.guiaRecepcion',compact("listaDetalles","kardex","fechaLarga"));
        return $pdf->stream("Guía de Recepción.pdf");
    }
    public function obtenerFechasLargas($fecha) {
        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
        $time = strtotime($fecha);
        return date('d',$time) . ' de ' . $meses[date('n',$time) - 1] . ' del ' . date('Y',$time);
    }
    public function show($proveedor)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = KardexProveedor::where('id', $proveedor)->where('estado','!=',0)->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'El kardex buscado a este proveedor no existe']);
        }
        $listaFardos = KardexFardo::select("id")->where('id_kardex',$kardex->id_kardex)->where('estado','!=',0)->get()->toArray();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existen fardos asociados al kardex']);
        }
        $listaFardo = array_map(function($v){
            return $v['id'];
        },$listaFardos);
        $listaDetalles = KardexFardoDetalle::obtenerProductos($kardex->id_proveedores,$listaFardo);
        return response()->json(["success" => ['lista' => $listaDetalles, 'fechaRecepcion' => $kardex->fechaRecepcion, 'observaciones' => $kardex->observaciones]]);
    }
    public function update(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardexProveedor);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = KardexProveedor::where('id', $request->proveedor)->where('estado','!=',0)->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'El kardex buscado a este proveedor no existe']);
        }
        $listaFardos = KardexFardo::select("id")->where('id_kardex',$kardex->id_kardex)->where('estado','!=',0)->get()->toArray();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existen fardos asociados al kardex']);
        }
        $listaFardo = array_map(function($v){
            return $v['id'];
        },$listaFardos);
        $kardex->update(['fechaRecepcion' => $request->fechaRecepcion,'observaciones' => $request->observaciones]);
        if($request->has('idDetalle')){
            for ($i=0; $i < count($request->idDetalle); $i++) { 
                KardexFardoDetalle::where(['id'=>$request->idDetalle[$i],'id_proveedor' => $kardex->id_proveedores])->where('estado','!=',0)->whereIn('id_fardo',$listaFardo)->update(['importe' => isset($request->importe[$i]) ? $request->importe[$i] : null]);
            }
        }
        return response()->json(["success" => 'Datos actualizados correctamente']);

    }
}
