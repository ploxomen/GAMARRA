<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Kardex as ModelsKardex;
use App\Models\KardexFardo;
use App\Models\KardexFardoDetalle;
use App\Models\Presentacion;
use App\Models\Productos;
use App\Models\Proveedores;
use Illuminate\Http\Request;

class Kardex extends Controller
{
    private $usuarioController;
    private $moduloKardex = "admin.kardex.index";
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
    public function obtenerKardexPendiente($cliente) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        return response()->json(['kardex' => ModelsKardex::verKardexPorTerminar($cliente)]);
    }
    public function cerrarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::where(['estado' => 1, 'id_cliente' => $request->cliente])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex asociado a este cliente']);
        }
        $nroFardoActual = $kardex->nroFardoActivo;
        if(empty($nroFardoActual)){
            return response()->json(['alerta' => 'No hay fardos pendientes por cerrar']);
        }
        $kardex->update(['nroFardoActivo' => null]);
        return response()->json(['success' => 'El fardo N° ' . $nroFardoActual . ' a sido cerrado correctamente' ]);
    }
    public function eliminarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::where(['estado' => 1, 'id_cliente' => $request->cliente])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex asociado a este cliente']);
        }
        $fardoKardex = KardexFardo::where(['estado' => 1,'id_kardex' => $kardex->id,'nro_fardo' => $request->fardo])->first();
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }    
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }    
        KardexFardoDetalle::where(['id_fardo' => $fardoKardex->id,'estado' => 1])->delete();
        KardexFardo::where(['estado' => 1,'id_kardex' => $kardex->id,'nro_fardo' => $request->fardo])->delete();
        $kardex->update(['nroFardoActivo' => null]);
        foreach (KardexFardo::where(['estado' => 1,'id_kardex' => $kardex->id])->get() as $k => $v) {
            $v->update(['nro_fardo' => $k + 1]);
        }
        return response()->json(['success' => 'El fardo N° ' . $request->fardo . ' a sido eliminado correctamente','nroFardo' => $kardex->nroFardoActivo]);
    }
    public function abrirFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::where(['estado' => 1, 'id_cliente' => $request->cliente])->first();
        if(empty($kardex)){
            return response()->json(['alerta' => 'No existe ningún kardex asociado a este cliente']);
        }
        $fardoKardex = KardexFardo::where(['estado' => 1,'id_kardex' => $kardex->id,'nro_fardo' => $request->fardo])->first();
        if(empty($fardoKardex)){
            return response()->json(['alerta' => 'No existe ningún fardo con el número ' . $request->fardo]);
        }    
        $kardex->update(['nroFardoActivo' => $request->fardo]);
        return response()->json(['success' => 'El fardo N° ' . $request->fardo . ' a sido abierto correctamente','nroFardo' => $request->fardo]);
    }
    public function agregarFardo(Request $request){
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::where(['estado' => 1, 'id_cliente' => $request->cliente])->first();
        if(empty($kardex)){
            $kardex = ModelsKardex::create([
                'id_cliente' => $request->cliente,
                'estado' => 1
            ]);
        }
        $nroFardo = $kardex->nroFardoActivo;
        if(empty($nroFardo)){
            $nroFardo = KardexFardo::where('id_kardex',$kardex->id)->count() + 1;
            KardexFardo::create([
                'id_kardex' => $kardex->id,
                'nro_fardo' => $nroFardo,
                'estado' => 1
            ]);
            ModelsKardex::where(['estado' => 1, 'id_cliente' => $request->cliente])->update(['nroFardoActivo' => $nroFardo]);
        }
        $fardoKardex = KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $nroFardo])->first();
        KardexFardoDetalle::create([
            'id_fardo' => $fardoKardex->id,
            'cantidad' => $request->cantidad,
            'id_proveedor' => $request->proveedor,
            'id_producto' => $request->producto,
            'id_presentacion' => $request->presentacion,
            'estado' => 1
        ]);
        $cantidadProductos = KardexFardoDetalle::where(['estado'=>1,'id_fardo' => $fardoKardex->id])->count();
        return response()->json(['success' => 'producto agregado correctamente', 'nroFardo' => $nroFardo,'cantidadProducto' => $cantidadProductos]);
    }
}
