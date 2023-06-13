<?php

use App\Http\Controllers\Categoria;
use App\Http\Controllers\Clientes;
use App\Http\Controllers\Kardex;
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
    });
    Route::prefix('almacen')->group(function () {
        Route::prefix('categoria')->group(function () {
            Route::get('/', [Categoria::class, 'index'])->name('admin.categoria.index');
            Route::post('listar', [Categoria::class, 'listar']);
            Route::get('listar/{categoria}', [Categoria::class, 'show']);
            Route::post('crear', [Categoria::class, 'store']);
            Route::post('editar/{categoria}', [Categoria::class, 'update']);
            Route::delete('eliminar/{categoria}', [Categoria::class, 'destroy']);
        });
        Route::prefix('producto')->group(function () {
            Route::get('/', [MisProductos::class, 'index'])->name('admin.producto.index');
            Route::post('listar', [MisProductos::class, 'listar']);
            Route::get('listar/{producto}', [MisProductos::class, 'show']);
            Route::post('crear', [MisProductos::class, 'store']);
            Route::post('editar/{producto}', [MisProductos::class, 'update']);
            Route::delete('eliminar/{producto}', [MisProductos::class, 'destroy']);
        });
        Route::prefix('kardex')->group(function () {
            Route::get('/', [Kardex::class, 'index'])->name('admin.kardex.index');
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
