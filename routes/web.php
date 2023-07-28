<?php

use App\Http\Controllers\Aduaneros;
use App\Http\Controllers\Articulo;
use App\Http\Controllers\Familia;
use App\Http\Controllers\Clientes;
use App\Http\Controllers\Kardex;
use App\Http\Controllers\KardexProveedores;
use App\Http\Controllers\MisProductos;
use App\Http\Controllers\Modulos;
use App\Http\Controllers\Proveedores;
use App\Http\Controllers\Rol;
use App\Http\Controllers\Usuario;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('intranet')->group(function(){
    Route::prefix('inicio')->group(function () {
        Route::get('/', [Usuario::class, 'index'])->name('home');
        Route::post('administrador', [Usuario::class, 'inicioAdministrador']);
    });
    Route::prefix('usuarios')->group(function(){
        Route::post('accion',[Usuario::class,'usuarioAccion']);
        Route::post('password',[Usuario::class,'cambioContrasena']);
        Route::get('cambio/rol/{rol}', [Usuario::class, 'cambioRol'])->name('cambiarRol');
        Route::get('miperfil', [Usuario::class, 'miPerfil'])->name('miPerfil');
        Route::post('miperfil/actualizar', [Usuario::class, 'actualizarPerfil']);
        Route::get('/',[Usuario::class,'listarUsuarios'])->name('admin.usuario.index');
        Route::get('cerrar/sesion', [Usuario::class, 'logoauth'])->name('cerrarSesion');
        Route::get('rol',[Rol::class,'viewRol'])->name('admin.rol.index');
        Route::get('modulo', [Modulos::class, 'index'])->name('admin.modulos.index');
        Route::post('modulo/accion', [Modulos::class, 'accionesModulos']);
        Route::post('rol/accion', [Rol::class, 'accionesRoles']);
    });
    Route::get('storage/{tipo}/{filename}', function ($tipo,$filename){
        $path = storage_path('app/'.$tipo . '/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    })->name("urlImagen");
    Route::prefix('compras')->group(function () {
        Route::prefix('proveedores')->group(function () {
            Route::get('/', [Proveedores::class, 'index'])->name("admin.compras.proveedores");
            Route::post('contacto/eliminar', [Proveedores::class, 'eliminarContacto']);
            Route::post('listar', [Proveedores::class, 'listar']);
            Route::get('listar/{proveedor}', [Proveedores::class, 'show']);
            Route::post('crear', [Proveedores::class, 'store']);
            Route::delete('eliminar/{proveedor}', [Proveedores::class, 'destroy']);
        });
    });
    Route::prefix('ventas')->group(function () {
        Route::prefix('clientes')->group(function () {
            Route::get('/', [Clientes::class, 'index'])->name('admin.ventas.clientes.index');
            Route::post('listar', [Clientes::class, 'listar']);
            Route::get('listar/{cliente}', [Clientes::class, 'show']);
            Route::post('crear', [Clientes::class, 'store']);
            Route::post('editar/{cliente}', [Clientes::class, 'update']);
            Route::delete('eliminar/{cliente}', [Clientes::class, 'destroy']);
            Route::get('contacto/eliminar/{contacto}', [Clientes::class, 'eliminarContacto']);
        });
        Route::prefix('kardex/proveedores')->group(function () {
            Route::get('/', [KardexProveedores::class, 'index'])->name('admin.proveedores.index');
            Route::post('listar', [KardexProveedores::class, 'listar']);
            Route::get('listar/{proveedor}', [KardexProveedores::class, 'show']);
            Route::get('reporte/{kardex}/{proveedor}', [KardexProveedores::class, 'verGuiaReporte']);
            Route::post('actualizar', [KardexProveedores::class, 'update']);
        });
    });
    Route::prefix('almacen')->group(function () {
        Route::prefix('familias')->group(function () {
            Route::get('/', [Familia::class, 'index'])->name('admin.familia.index');
            Route::post('listar', [Familia::class, 'listar']);
            Route::get('listar/{familia}', [Familia::class, 'show']);
            Route::post('crear', [Familia::class, 'store']);
            Route::post('editar/{familia}', [Familia::class, 'update']);
            Route::delete('eliminar/{familia}', [Familia::class, 'destroy']);
            Route::get('subfamilia/eliminar/{subfamilia}', [Familia::class, 'eliminarSubfamilia']);
        });
        Route::prefix('aduaneros')->group(function () {
            Route::get('/', [Aduaneros::class, 'index'])->name('admin.aduaneros.index');
            Route::post('listar', [Aduaneros::class, 'listar']);
            Route::get('listar/{aduanero}', [Aduaneros::class, 'show']);
            Route::post('crear', [Aduaneros::class, 'store']);
            Route::post('editar/{aduanero}', [Aduaneros::class, 'update']);
            Route::delete('eliminar/{aduanero}', [Aduaneros::class, 'destroy']);
        });
        Route::prefix('producto')->group(function () {
            Route::get('/', [MisProductos::class, 'index'])->name('admin.producto.index');
            Route::post('listar', [MisProductos::class, 'listar']);
            Route::get('listar/{producto}', [MisProductos::class, 'show']);
            Route::post('crear', [MisProductos::class, 'store']);
            Route::post('editar/{producto}', [MisProductos::class, 'update']);
            Route::delete('eliminar/{producto}', [MisProductos::class, 'destroy']);
            Route::get('familia/{familia}', [MisProductos::class, 'obtenerSubfamilias']);
            Route::get('subfamilia/{subfamilia}', [MisProductos::class, 'obtenerArticulos']);
        });
        Route::prefix('kardex')->group(function () {
            Route::get('/', [Kardex::class, 'index'])->name('admin.kardex.index');
            Route::get('todos', [Kardex::class, 'misKardexIndex'])->name('admin.miskardex.index');
            Route::post('actualizar/fardos', [Kardex::class, 'actualizarValoresKardex']);
            Route::post('actualizar/tasa', [Kardex::class, 'actualizarTasas']);
            Route::post('todos/listar', [Kardex::class, 'misKardex']);
            Route::get('pendiente/{cliente}', [Kardex::class, 'obtenerKardexPendiente']);
            Route::get('pendiente/editar/{cliente}/{kardex}', [Kardex::class, 'obtenerKardex']);
            Route::get('reportes/facturacion/{kardex}', [Kardex::class, 'generarPreFacturaCliente']);
            Route::get('reportes/packing/{kardex}', [Kardex::class, 'generarReportesPackingList']);
            Route::get('cliente/reporte/{kardex}', [Kardex::class, 'consultaReporteCliente']);
            Route::get('cliente/reporte/kardex/{kardex}/{cliente}', [Kardex::class, 'reporteClienteKardex']);
            Route::post('pendiente/guardar', [Kardex::class, 'agregarFardo']);
            Route::post('pendiente/generar', [Kardex::class, 'generarKardex']);
            Route::post('pendiente/cerrar', [Kardex::class, 'cerrarFardo']);
            Route::post('pendiente/eliminar', [Kardex::class, 'eliminarFardo']);
            Route::post('pendiente/abrir', [Kardex::class, 'abrirFardo']);
            Route::post('listar', [Kardex::class, 'listar']);
            Route::get('listar/{kardex}', [Kardex::class, 'show']);
            Route::post('crear', [Kardex::class, 'store']);
            Route::post('editar/{kardex}', [Kardex::class, 'update']);
            Route::delete('eliminar/{kardex}', [Kardex::class, 'destroy']);
        });
    });
});

Route::get("/",function(){
    return redirect()->route('login');
});
Route::middleware(['guest'])->prefix('intranet')->group(function () {
    Route::get('acceso', [Usuario::class, 'loginView'])->name('login');
    Route::get("restaurar", [Usuario::class, 'retaurarContra'])->name('restaurarContra');
    Route::get("restaurar/salir", [Usuario::class, 'salirLoginFirst'])->name('salirRestaurar');
    Route::post("autenticacion", [Usuario::class, 'autenticacion']);
    Route::post("restaurar", [Usuario::class, 'restaurarContrasena']);
});
