<?php

namespace App\Http\Controllers;

use App\Exports\AdministradorFamiliaSub;
use App\Http\Controllers\Usuario;
use App\Models\Familia as ModelsFamilia;
use App\Models\SubFamilias;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Familia extends Controller
{
    private $moduloFamilia = "admin.familia.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        return view("productos.familia",compact("modulos"));
    }
    public function listar(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $familia = ModelsFamilia::with("subFamila")->get();
        return DataTables::of($familia)->toJson();
    }
    public function store(Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(ModelsFamilia::canitdadFamilias($request->codigoFamilia)){
            return response()->json(['alerta' => 'El código ' . $request->codigoFamilia . ' de la familia ya se encuentra registrado, por favor establesca otro código']);
        }
        if(isset($request->subfamiliaCodigo)){
            for ($i=0; $i < count($request->subfamiliaCodigo); $i++) {
                if(isset($request->subfamiliaCodigo[$i]) && SubFamilias::canitdadSubFamilias($request->subfamiliaCodigo[$i])){
                    return response()->json(['alerta' => 'El código ' . $request->subfamiliaCodigo[$i] . ' de la subfamilia ya se encuentra registrado, por favor establesca otro código']);
                }
            }
        }
        DB::beginTransaction();
        try {
            $familia = ModelsFamilia::create(['codigo' => $request->codigoFamilia,'nombre' => $request->nombreFamilia,'estado' => 1]);
            if(isset($request->subfamiliaCodigo)){
                for ($i=0; $i < count($request->subfamiliaCodigo); $i++) {
                    $subFamilia = [
                        'id_familia' => $familia->id,
                        'codigo' => isset($request->subfamiliaCodigo[$i]) ? $request->subfamiliaCodigo[$i] : null,
                        'nombre' => isset($request->subfamiliaNombre[$i]) ? $request->subfamiliaNombre[$i] : null,
                        'estado' => 1
                    ];
                    SubFamilias::create($subFamilia);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Familia agregada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
        }
    }
    public function reporteExcel() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $familias = ModelsFamilia::orderBy('codigo')->get();
        return Excel::download(new AdministradorFamiliaSub($familias,$familias->count()),'reporte_familias.xlsx');
    }
    public function reportePdf() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($verif['session'])){
            return redirect()->route("home");
        }
        $familias = ModelsFamilia::orderBy('codigo')->get();
        return Pdf::loadView('productos.reportes.familiaPdf',compact("familias"))->stream("reporte_familias.pdf");
    }
    public function show($familia, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        $listaFamilia = ModelsFamilia::where('id',$familia)->with('subFamila:id_familia,id,codigo,nombre')->first();
        return response()->json(["success" => $listaFamilia]);
    }
    public function update($familia, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(ModelsFamilia::canitdadFamiliasEditar($request->codigoFamilia,$familia)){
            return response()->json(['alerta' => 'El código ' . $request->codigoFamilia . ' de la familia ya se encuentra registrado, por favor establesca otro código']);
        }
        if(isset($request->subfamiliaCodigo)){
            for ($i=0; $i < count($request->subfamiliaCodigo); $i++) {
                if(isset($request->subfamiliaCodigo[$i])){
                    $consulta = isset($request->idSubfamilia[$i]) ? SubFamilias::canitdadSubFamiliasEditar($request->subfamiliaCodigo[$i],$request->idSubfamilia[$i]) : SubFamilias::canitdadSubFamilias($request->subfamiliaCodigo[$i]);
                    if($consulta){
                        return response()->json(['alerta' => 'El código ' . $request->subfamiliaCodigo[$i] . ' de la subfamilia ya se encuentra registrado, por favor establesca otro código']);
                    }
                }
            }
        }
        DB::beginTransaction();
        try {
            $familiaModal = ModelsFamilia::where("id",$familia)->update(['codigo' => $request->codigoFamilia,'nombre' => $request->nombreFamilia,'estado' => $request->has("estado") ? 1 : 0]);
            if(isset($request->subfamiliaCodigo)){
                for ($i=0; $i < count($request->subfamiliaCodigo); $i++) {
                    $subFamilia = [
                        'codigo' => isset($request->subfamiliaCodigo[$i]) ? $request->subfamiliaCodigo[$i] : null,
                        'nombre' => isset($request->subfamiliaNombre[$i]) ? $request->subfamiliaNombre[$i] : null,
                        'estado' => 1
                    ];
                    if(isset($request->idSubfamilia[$i])){
                        SubFamilias::where(['id' => $request->idSubfamilia[$i],'id_familia' => $familia])->update($subFamilia);
                    }else{
                        $subFamilia['id_familia'] = $familia;
                        SubFamilias::create($subFamilia);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => 'Familia actualizada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
        }
    }
    public function eliminarSubfamilia(SubFamilias $subfamilia)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $subfamilia->delete();
        return response()->json(['success' => 'subfamilia eliminada correctamente']);
    }
    public function destroy($familia, Request $request)
    {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloFamilia);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        if(ModelsFamilia::canitdadProductos($familia) > 0){
            return response()->json(['alerta' => 'Para eliminar la familia y subfamilia primero elimine los productos asociados a ella']);
        }
        SubFamilias::where('id_familia',$familia)->delete();
        ModelsFamilia::find($familia)->delete();
        return response()->json(['success' => 'familia y subfamilia eliminada correctamente']);
    }
}
