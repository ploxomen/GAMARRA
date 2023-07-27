<?php

namespace App\Http\Controllers;

use App\Exports\KardexExport;
use App\Exports\PreFacturacion;
use App\Models\Aduanero;
use App\Models\Clientes;
use App\Models\Kardex as ModelsKardex;
use App\Models\KardexFardo;
use App\Models\KardexFardoDetalle;
use App\Models\KardexProveedor;
use App\Models\Presentacion;
use App\Models\Productos;
use App\Models\Proveedores;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Kardex extends Controller
{
    private $usuarioController;
    private $moduloKardex = "admin.kardex.index";
    private $moduloMisKardex = "admin.miskardex.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $productos = Productos::where('estado',1)->get();
        $clientes = Clientes::where('estado',1)->get();
        $proveedores = Proveedores::where('estado',1)->get();
        $presentaciones = Presentacion::obtenerPresentaciones();
        return view("kardex.generar",compact("modulos","clientes","productos","proveedores","presentaciones"));
    }
    public function actualizarValoresKardex(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = $request->has("idKardex") ? ModelsKardex::find($request->idKardex) : ModelsKardex::where('estado', 1)->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex']);
        }
        $fardoCliente = KardexFardo::where(['id_kardex' => $kardex->id,'id_cliente' => $request->cliente,'nro_fardo' => $request->fardo])->first();
        if(empty($fardoCliente)){
            return response()->json(['alerta' => 'No existe ningún número de fardo '. $request->fardo .' asociado a este cliente']);
        }
        $columnas = [
            'proveedor' => 'id_proveedor',
            'presentacion' => 'id_presentacion',
            'producto' => 'id_producto',
            'cantidad' => 'cantidad',
            'kilaje' => 'kilaje',
            'costo' => 'precio',
            'tasa' => 'tasa',
            'tasa_extranjera' => 'tasa_extranjera'
        ];
        $columnaActualizar = $columnas[$request->campo];
        $db = $columnaActualizar == "kilaje" || $columnaActualizar == 'tasa' || $columnaActualizar == 'tasa_extranjera'  ? $fardoCliente->update([$columnaActualizar => $request->valor]) : KardexFardoDetalle::where(['id_fardo' => $fardoCliente->id,'id' => $request->idDetalle])->update([$columnaActualizar => $request->valor]);
        return response()->json(['success' => $db ? $request->campo . ' se modifico de manera correcta' : $request->campo . ' no se a podido modificar']);
    }
    public function generarReportesPackingList(ModelsKardex $kardex){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        return Excel::download(new KardexExport($kardex), 'kardex_'. str_pad($kardex->id,5,'0',STR_PAD_LEFT). '.xlsx');
    }
    public function generarPreFacturaCliente(ModelsKardex $kardex)  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        return Excel::download(new PreFacturacion($kardex), 'prefacturacion_clientes_'. str_pad($kardex->id,5,'0',STR_PAD_LEFT). '.xlsx');
    }
    public function obtenerKardexPendiente($cliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['kardex' => ModelsKardex::verKardexPorTerminar($cliente)]);
    }
    public function obtenerKardex($cliente,$kardex) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['kardex' => ModelsKardex::verKardexPorTerminar($cliente,$kardex)]);
    }
    public function consultaReporteCliente(ModelsKardex $kardex) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $clientes = $kardex->fardos()->select("id_cliente")->groupBy("id_cliente")->get();
        foreach ($clientes as $cliente) {
            $cliente->nombreCliente = $cliente->clientes->nombreCliente;
        }
        return response()->json(['success' => $clientes]);
    }
    public function reporteClienteKardex(ModelsKardex $kardex,$cliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $fardos = $kardex->fardos()->where('id_cliente',$cliente)->get();
        return Pdf::loadView("kardex.reportesPdf.kardexCliente",compact("fardos"))->stream("kardex_cliente_" . $kardex->id . '_' . $cliente .'.pdf');
    }
    public function cerrarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = $request->has("idKardex") ? ModelsKardex::findOrFail($request->idKardex)  : ModelsKardex::where(['estado' => 1])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex']);
        }
        $nroFardoActual = $kardex->nroFardoActivo;
        if(empty($nroFardoActual)){
            return response()->json(['alerta' => 'No hay fardos pendientes por cerrar']);
        }
        $kardex->update(['nroFardoActivo' => null]);
        return response()->json(['success' => 'El fardo N° ' . $nroFardoActual . ' a sido cerrado correctamente' ]);
    }
    public function eliminarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = $request->has("idKardex") ? ModelsKardex::findOrFail($request->idKardex)  : ModelsKardex::where(['estado' => 1])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex']);
        }
        $fardoKardex = KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $request->fardo,'id_cliente' => $request->cliente])->first();
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }    
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }
        KardexFardoDetalle::where(['id_fardo' => $fardoKardex->id])->delete();
        KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $request->fardo,'id_cliente' => $request->cliente])->delete();
        $kardex->update(['nroFardoActivo' => null]);
        foreach (KardexFardo::where(['id_kardex' => $kardex->id])->get() as $k => $v) {
            $v->update(['nro_fardo' => $k + 1]);
        }
        return response()->json(['success' => 'El fardo N° ' . $request->fardo . ' a sido eliminado correctamente','nroFardo' => $kardex->nroFardoActivo]);
    }
    public function abrirFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = $request->has("idKardex") ? ModelsKardex::findOrFail($request->idKardex)  : ModelsKardex::where(['estado' => 1])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex']);
        }
        $fardoKardex = KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $request->fardo])->first();
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }    
        $kardex->update(['nroFardoActivo' => $request->fardo]);
        return response()->json(['success' => 'El fardo N° ' . $request->fardo . ' a sido abierto correctamente','nroFardo' => $request->fardo]);
    }
    public function generarKardex(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        DB::beginTransaction();
        try{
            $kardex = ModelsKardex::where(['estado' => 1])->first();
            if(empty($kardex)){
                return response()->json(['alerta' => 'No existe ningún kardex']);
            }
            foreach (KardexFardo::where(['estado' => 1,'id_kardex' => $kardex->id])->get() as $vFardo) {
                KardexFardoDetalle::where(['id_fardo' => $vFardo->id])->update(['estado' => 2]);
                $cantidad = KardexFardoDetalle::where(['id_fardo' => $vFardo->id])->sum('cantidad');
                $vFardo->update(['cantidad' => $cantidad,'estado' => 2]);
            }
            $cantidad = KardexFardo::where(['estado' => 2,'id_kardex' => $kardex->id])->sum('cantidad');
            $kilaje = KardexFardo::where(['estado' => 2,'id_kardex' => $kardex->id])->sum('kilaje');
            $kardex->update(['nroFardoActivo' => null,'estado' => 2,'cantidad' => $cantidad,'kilaje' => $kilaje]);
            DB::commit();
            return response()->json(['success' => 'El kardex se generó correctamente']);
        }catch(\Exception $th){
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function misKardex()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::misKardex();
        return DataTables::of($kardex)->toJson();
    }
    public function misKardexIndex()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $productos = Productos::where('estado',1)->get();
        $clientes = Clientes::where('estado',1)->get();
        $proveedores = Proveedores::where('estado',1)->get();
        $presentaciones = Presentacion::obtenerPresentaciones();
        return view("kardex.misKardex",compact("modulos","productos","clientes","proveedores","presentaciones"));
    }
    public function agregarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        $verif2 = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session']) && isset($verif2['session'])){
            return response()->json(['session' => true]);
        }
        $aduanero = Aduanero::where(['estado' => 1,'principal' => 1])->first();
        if(empty($aduanero)){
            return response()->json(['alerta' => 'No se a definido un agente de aduanas principal']);
        }
        $kardex = $request->has("idKardex") ? ModelsKardex::findOrFail($request->idKardex)  : ModelsKardex::where(['estado' => 1])->first();
        if(empty($kardex)){
            $kardex = ModelsKardex::create([
                'estado' => 1
            ]);
        }
        $nroFardo = $kardex->nroFardoActivo;
        if(empty($nroFardo)){
            $cliente = Clientes::find($request->cliente);
            $nroFardo = KardexFardo::where('id_kardex',$kardex->id)->count() + 1;
            KardexFardo::create([
                'id_cliente' => $cliente->id,
                'id_kardex' => $kardex->id,
                'nro_fardo' => $nroFardo,
                'tasa' => $cliente->tasa,
                'tasa_extranjera' => $aduanero->tasa,
                'estado' => $request->has("idKardex") ? 2 : 1
            ]);
            $kardex->update(['nroFardoActivo' => $nroFardo]);
        }
        $fardoKardex = KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $nroFardo,'id_cliente' => $request->cliente])->first();
        $producto = Productos::find($request->producto);
        $nuevoDetalle = KardexFardoDetalle::create([
            'id_fardo' => $fardoKardex->id,
            'cantidad' => $request->cantidad,
            'id_proveedor' => $request->proveedor,
            'id_producto' => $producto->id,
            'id_presentacion' => $request->presentacion,
            'precio' => $producto->precioVenta,
            'estado' => $request->has("idKardex") ? 2 : 1
        ]);
        KardexProveedor::updateOrCreate([
            'id_kardex' => $kardex->id,
            'id_proveedores' => $request->proveedor
        ],[
            'estado' => 1,
            'fechaRecepcion' => date('Y-m-d')
        ]);
        $cantidadProductos = KardexFardoDetalle::where(['id_fardo' => $fardoKardex->id])->count();
        $response = [
            'success' => 'producto agregado correctamente', 
            'nroFardo' => $nroFardo,
            'cantidadProducto' => $cantidadProductos,
            'idDetalle' => $nuevoDetalle->id,
            'kilaje' => $fardoKardex->kilaje,
            'tasa' => $fardoKardex->tasa,
            'precioProducto' => $producto->precioVenta,
            'tasaExtranjera' => $fardoKardex->tasa_estranjera,
            'tipo' => true
        ];
        if(!$request->has('idKardex')){
            $response['tasa'] = '';
            $response['tasaExtranjera'] = '';
            $response['precioProducto'] = '';
            $response['tipo'] = false;
        }
        return response()->json($response);
    }
}
