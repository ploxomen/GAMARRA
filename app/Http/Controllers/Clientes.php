<?php

namespace App\Http\Controllers;

use App\Exports\AdministradorClientes;
use App\Http\Controllers\Usuario;
use App\Models\Categoria;
use App\Models\Clientes as ModelsClientes;
use App\Models\ClientesContactos;
use App\Models\ClientesTasas;
use App\Models\Paises;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Models\UsuarioRol;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class Clientes extends Controller
{
    private $usuarioController;
    private $moduloCliente = "admin.ventas.clientes.index";
    function __construct()
    {
        $this->usuarioController = new Usuario();
    }
    public function index()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        $paises = Paises::all()->where('estado',1);
        $categorias = Categoria::all()->where('estado',1);
        return view("ventas.clientes",compact("modulos","tiposDocumentos","paises","categorias"));
    }
    public function listar()
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $clientes = ModelsClientes::obenerClientes();
        return DataTables::of($clientes)->toJson();
    }
    public function reporteExcel() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = ModelsClientes::obenerClientes(true);
        return Excel::download(new AdministradorClientes($clientes,$clientes->count()),'reportes_clientes.xlsx');
    }
    public function reportePdf() {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $clientes = ModelsClientes::obenerClientes(true);
        return Pdf::loadView('ventas.reportes.clientesPdf',compact("clientes"))->setPaper('A4','landscape')->stream("reporte_clientes.pdf");
    }
    public function store(Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $repetidos = User::where(['correo' => $request->correo])->count();
        if ($repetidos > 0) {
            return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
        }
        $rolCliente = Rol::where('nombreRol','cliente')->first();
        if(empty($rolCliente)){
            return response()->json(['alerta' => 'Para crear una cuenta de cliente se necesita el rol Cliente por favor registre el rol']);
        }
        if(!empty($request->nro_documento)){
            $consultaCliente = ModelsClientes::where(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento])->first();
            if(!empty($consultaCliente)){
                $tipoDocumento = TipoDocumento::find($request->tipo_documento);
                $tipoDocumento = empty($tipoDocumento) ? 'No definido' : $tipoDocumento->documento;
                return response()->json(['alerta' => 'No se puede registrar el tipo de documento <b>' . $tipoDocumento  . '</b> con el número <b>' . $request->nro_documento . '</b> porque ya se encuentra asociado a <b>' . $consultaCliente->nombreCliente .'</b>']);
            }
        }
        $datosUsuario = $request->only("correo","password","telefono","celular","direccion");
        $datosUsuario['password'] = Hash::make($datosUsuario['password']);
        $datosUsuario['estado'] = 0;
        $datosUsuario['nombres'] = $request->nombreCliente;
        DB::beginTransaction();
        try {
            $usuario = User::create($datosUsuario);
            UsuarioRol::create(['rolFk' => $rolCliente->id,'usuarioFk' => $usuario->id]);
            $cliente = ModelsClientes::create(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento,'id_usuario' => $usuario->id,'id_pais' => $request->id_pais,'nombreCliente' => $request->nombreCliente,'estado' => 1]);
            if(isset($request->id_categoria)){
                for ($i=0; $i < count($request->id_categoria); $i++) {
                    $tasa = [
                        'id_cliente' => $cliente->id,
                        'id_categoria' => isset($request->id_categoria[$i]) ? $request->id_categoria[$i] : null,
                        'tasa' => isset($request->tasa[$i]) ? $request->tasa[$i] : 0
                    ];
                    ClientesTasas::create($tasa);
                }
            }
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'idCliente' => $cliente->id,
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    ClientesContactos::create($contactos);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Cliente creado correctamente, recuerde que su contraseña temporal es ' . $request->password]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage(),'codigo' => $th->getCode()]);
        }
    }
    public function show($cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        return response()->json(['cliente' => ModelsClientes::obenerCliente($cliente) ]);
    }
    public function update(ModelsClientes $cliente, Request $request)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if(!empty($request->nro_documento)){
                $consultaCliente = ModelsClientes::where(['tipo_documento' => $request->tipo_documento, 'nro_documento' => $request->nro_documento])->where('id','!=',$cliente->id)->first();
                if(!empty($consultaCliente)){
                    $tipoDocumento = TipoDocumento::find($request->tipo_documento);
                    $tipoDocumento = empty($tipoDocumento) ? 'No definido' : $tipoDocumento->documento;
                    return response()->json(['alerta' => 'No se puede registrar el tipo de documento <b>' . $tipoDocumento  . '</b> con el número <b>' . $request->nro_documento . '</b> porque ya se encuentra asociado a <b>' . $consultaCliente->nombreCliente .'</b>']);
                }
            }
            $datosUsuario = $request->only("telefono","celular","direccion");
            $datosUsuario['nombres'] = $request->nombreCliente;
            $datosCliente = $request->only('tipo_documento','nro_documento',"nombreCliente","id_pais");
            $datosCliente['estado'] = $request->has("estado") ? 1 : 0;
            User::where('id',$cliente->id_usuario)->update($datosUsuario);
            $cliente->update($datosCliente);
            if(isset($request->id_categoria)){
                for ($i=0; $i < count($request->id_categoria); $i++) {
                    $tasa = [
                        'id_categoria' => isset($request->id_categoria[$i]) ? $request->id_categoria[$i] : null,
                        'tasa' => isset($request->tasa[$i]) ? $request->tasa[$i] : 0
                    ];
                    if(isset($request->id_tasa[$i]) && $request->id_tasa[$i] != '0'){
                        ClientesTasas::where(['id' => $request->id_tasa[$i],'id_cliente' => $cliente->id])->update($tasa);
                    }else{
                        $tasa['id_cliente'] = $cliente->id;
                        ClientesTasas::create($tasa);
                    }
                }
            }
            if(isset($request->contactoNombres)){
                for ($i=0; $i < count($request->contactoNombres); $i++) {
                    $contactos = [
                        'nombreContacto' => isset($request->contactoNombres[$i]) ? $request->contactoNombres[$i] : null,
                        'numeroContacto' => isset($request->contactoNumero[$i]) ? $request->contactoNumero[$i] : null
                    ];
                    if(isset($request->idContacto[$i])){
                        ClientesContactos::where(['id' => $request->idContacto[$i],'idCliente' => $cliente->id])->update($contactos);
                    }else{
                        $contactos['idCliente'] = $cliente->id;
                        ClientesContactos::create($contactos);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => 'Cliente actualizado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function eliminarTasa($cliente,$tasa)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        ClientesTasas::where(['id_cliente' => $cliente,'id' => $tasa])->delete();
        return response()->json(['success' => 'tasa eliminada correctamente']);
    }
    public function eliminarContacto(ClientesContactos $contacto)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        $contacto->delete();
        return response()->json(['success' => 'contacto eliminado correctamente']);
    }
    public function destroy(ModelsClientes $cliente)
    {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloCliente);
        if(isset($verif['session'])){
            return response()->json(['session' => true]); 
        }
        DB::beginTransaction();
        try {
            if($cliente->fardos()->count() > 0){
                return response()->json(['alerta' => 'Este cliente no puede ser eliminado debido a que está asociado con uno o varios kardex']); 
            }
            $cliente->delete();
            DB::commit();
            return response()->json(['success' => 'cliente eliminado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
