<?php

namespace App\Http\Controllers;

use App\Exports\AdministradorProductos;
use App\Http\Controllers\Usuario;
use App\Models\Familia;
use App\Models\Productos;
use App\Models\SubFamilias;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MisProductos extends Controller
{
    private $moduloProducto = "admin.producto.index";
    private $usuarioController;
    
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $familias = Familia::all()->where('estado',1);
        return view("productos.productos",compact("modulos","familias"));
    }
    public function listar(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $productos = Productos::obtenerProductos();
        return DataTables::of($productos)->toJson();
    }
    public function reporteExcel() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $productos = Productos::obtenerProductos(true);
        return Excel::download(new AdministradorProductos($productos,$productos->count()),'reportes_productos.xlsx');
    }
    public function reportePdf() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $productos = Productos::obtenerProductos(true);
        return Pdf::loadView('productos.reportes.productosPdf',compact("productos"))->setPaper('A4','landscape')->stream("reporte_productos.pdf");
    }
    public function obtenerSubfamilias(Familia $familia, Request $request){
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        return response()->json(['success' => $familia->subFamila()->select("id","codigo","nombre")->where('estado',1)->get()]);
    }
    public function obtenerArticulos(SubFamilias $subfamilia, Request $request) {
        if(!$request->ajax()){
            return response()->json($this->usuarioController->errorPeticion);
        }
        $accessModulo = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($accessModulo['session'])){
            return response()->json($accessModulo);
        }
        return response()->json(['success' => $subfamilia->articulos()->select("id","codigo","nombre")->where('estado',1)->get()]);

    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            if(Productos::cantidadProductosCodigo($request->codigo)){
                return response()->json(['alerta' => 'El código ' . $request->codigo . ' del producto ya se encuentra registrado, por favor establesca otro código']);
            }
            $datos = $request->only("codigo","nombreProducto","descripcion","precioVenta","id_subfamilia");
            if($request->has('urlImagen')){
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $datos['urlImagen'];
            }
            $datos['estado'] = 1;
            Productos::create($datos);
            DB::commit();
            return response()->json(['success' => 'producto agregado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function show($producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $modelProducto = Productos::obtenerProductoEditar($producto);
        $modelProducto->urlProductos = !empty($modelProducto->productoImagen) ? route("urlImagen",["productos",$modelProducto->productoImagen]) : null;
        return response()->json(['producto' => $modelProducto]);
    }
    public function update(Productos $producto, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $urlImage = null;
        DB::beginTransaction();
        try {
            if(Productos::cantidadProductosCodigoEditar($request->codigo,$producto->id)){
                return response()->json(['alerta' => 'El código ' . $request->codigo . ' del producto ya se encuentra registrado, por favor establesca otro código']);
            }
            $datos = $request->only("codigo","nombreProducto","descripcion","precioVenta","id_subfamilia");
            if($request->has('urlImagen')){
                if(!empty($producto->urlImagen) && Storage::disk('productos')->exists($producto->urlImagen)){
                    Storage::disk('productos')->delete($producto->urlImagen);
                }
                $datos['urlImagen'] = $this->guardarArhivo($request,'urlImagen',"productos");
                $urlImage = $request->urlImagen;
            }
            $datos['estado'] = $request->has('estado');
            $producto->update($datos);
            DB::commit();
            return response()->json(['success' => 'producto actualizado correctamente']);
        } catch (\Throwable $th) {
            if(Storage::disk('productos')->exists($urlImage)){
                Storage::disk('productos')->delete($urlImage);
            }
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function destroy(Productos $producto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloProducto);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if($producto->detalleFardo()->count() > 0){
                return response()->json(['alerta' => 'Este producto no puede ser eliminado debido a que está asociado con uno o varios kardex']); 
            }
            if(!empty($producto->urlImagen) && Storage::disk('productos')->exists($producto->urlImagen)){
                Storage::disk('productos')->delete($producto->urlImagen);
            }
            $producto->delete();
            DB::commit();
            return response()->json(['success' => 'producto eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function guardarArhivo($request,$key,$disk)
    {
        $nombreOriginal = $request->file($key)->getClientOriginalName();
        $nombreArchivo = pathinfo($nombreOriginal,PATHINFO_FILENAME);
        $extension = $request->file($key)->getClientOriginalExtension();
        $archivoNombreAlmacenamiento = $nombreArchivo.'_'.time().'.'.$extension;
        $request->file($key)->storeAs($disk,$archivoNombreAlmacenamiento);
        return $archivoNombreAlmacenamiento;
    }
}
