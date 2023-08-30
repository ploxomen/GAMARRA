<?php

namespace App\Http\Controllers;

use App\Exports\AdministradorAduaneros;
use App\Models\Aduanero;
use App\Models\Articulo as ModelsArticulo;
use App\Models\Familia;
use App\Models\Paises;
use App\Models\TipoDocumento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Aduaneros extends Controller
{
    private $moduloAduanero = "admin.aduaneros.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $paises = Paises::where('estado',1)->get();
        return view("ventas.aduanero",compact("modulos","tiposDocumentos","paises"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduaneros = Aduanero::obtenerDatos();
        return DataTables::of($aduaneros)->toJson();
    }
    public function reporteExcel() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $aduaneros = Aduanero::obtenerDatos(true);
        return Excel::download(new AdministradorAduaneros($aduaneros,$aduaneros->count()),'reporte_agentes_aduanas.xlsx');
    }
    public function reportePdf() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $aduaneros = Aduanero::obtenerDatos(true);
        return Pdf::loadView('ventas.reportes.aduaneroPdf',compact("aduaneros"))->setPaper('A4','landscape')->stream("reporte_agentes_aduanas.pdf");
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $datos = $request->only("tipo_documento","nro_documento","nombre_completo","id_pais","tasa");
        if(!empty($request->nro_documento)){
            $agente = Aduanero::where(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento])->first();
            if(!empty($agente)){
                $tipoDocumento = TipoDocumento::find($request->tipo_documento);
                $tipoDocumento = empty($tipoDocumento) ? 'No definido' : $tipoDocumento->documento;
                return response()->json(['alerta' => 'No se puede registrar el tipo de documento <b>' . $tipoDocumento  . '</b> con el número <b>' . $request->nro_documento . '</b> porque ya se encuentra asociado a <b>' . $agente->nombre_completo .'</b>']);
            }
        }
        
        if($request->has("principal")){
            Aduanero::where('estado',1)->update(['principal' => 0]);
            $datos['principal'] = 1;
        }
        $datos['estado'] = 1;

        Aduanero::create($datos);
        return response()->json(['success' => 'aduanero creado correctamente']);
    }
    public function show($aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduaneroInfo = Aduanero::find($aduanero);
        return response()->json(["success" => $aduaneroInfo]);
    }
    public function update($aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(!empty($request->nro_documento)){
            $agente = Aduanero::where(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento])->where('id','!=',$aduanero)->first();
            if(!empty($agente)){
                $tipoDocumento = TipoDocumento::find($request->tipo_documento);
                $tipoDocumento = empty($tipoDocumento) ? 'No definido' : $tipoDocumento->documento;
                return response()->json(['alerta' => 'No se puede registrar el tipo de documento <b>' . $tipoDocumento  . '</b> con el número <b>' . $request->nro_documento . '</b> porque ya se encuentra asociado a <b>' . $agente->nombre_completo .'</b>']);
            }
        }
        Aduanero::find($aduanero)->where('estado',1)->update(['principal' => 0]);
        $datos = $request->only("tipo_documento","nro_documento","nombre_completo","id_pais","tasa");
        if($request->has("principal")){
            Aduanero::where('estado',1)->update(['principal' => 0]);
            $datos['principal'] = 1;
        }
        $datos['estado'] = $request->has("estado") ? 1 : 0;
        Aduanero::find($aduanero)->update($datos);
        return response()->json(['success' => 'aduanero modificado correctamente']);
    }
    
    public function destroy(Aduanero $aduanero, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloAduanero);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $aduanero->delete();
        return response()->json(['success' => 'aduanero eliminado correctamente']);
    }
}
