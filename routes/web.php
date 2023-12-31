<?php

use App\Http\Controllers\Aduaneros;
use App\Http\Controllers\Categoria;
use App\Http\Controllers\Familia;
use App\Http\Controllers\Clientes;
use App\Http\Controllers\FacturacionElectronica;
use App\Http\Controllers\Kardex;
use App\Http\Controllers\KardexProveedores;
use App\Http\Controllers\MisProductos;
use App\Http\Controllers\Modulos;
use App\Http\Controllers\Proveedores;
use App\Http\Controllers\Ranking;
use App\Http\Controllers\RapiFac;
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
            Route::get('reportes/excel', [Proveedores::class, 'reporteExcel']);
            Route::get('reportes/pdf', [Proveedores::class, 'reportePdf']);
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
            Route::get('reportes/excel', [Clientes::class, 'reporteExcel']);
            Route::get('reportes/pdf', [Clientes::class, 'reportePdf']);
            Route::post('listar', [Clientes::class, 'listar']);
            Route::get('listar/{cliente}', [Clientes::class, 'show']);
            Route::post('crear', [Clientes::class, 'store']);
            Route::post('editar/{cliente}', [Clientes::class, 'update']);
            Route::delete('eliminar/{cliente}', [Clientes::class, 'destroy']);
            Route::get('contacto/eliminar/{contacto}', [Clientes::class, 'eliminarContacto']);
            Route::delete('tasa/eliminar/{cliente}/{tasa}', [Clientes::class, 'eliminarTasa']);
        });
        Route::prefix('kardex/proveedores')->group(function () {
            Route::get('/', [KardexProveedores::class, 'index'])->name('admin.proveedores.index');
            Route::post('listar', [KardexProveedores::class, 'listar']);
            Route::get('listar/{kardex}/{proveedor}/{cliente}', [KardexProveedores::class, 'show']);
            Route::get('reporte/{kardex}/{proveedor}/{cliente}', [KardexProveedores::class, 'verGuiaReporte']);
            Route::post('actualizar', [KardexProveedores::class, 'update']);
        });
    });
    Route::prefix('facturacion-electronica')->group(function () {
        Route::get('facturar', [FacturacionElectronica::class, 'indexFactura'])->name('facturacion.facturar.index');
        Route::post('facturar/listar', [FacturacionElectronica::class, 'misFacturaciones']);
        Route::post('facturar/eliminar', [FacturacionElectronica::class, 'eliminarFacturaElectronica']);
        Route::prefix('rapifac')->group(function () {
            Route::get('token', [RapiFac::class, 'obtenerToken']);
        });

    });
    Route::prefix('almacen')->group(function () {
        Route::prefix('categorias')->group(function () {
            Route::get('/', [Categoria::class, 'index'])->name('admin.categoria');
            Route::get('listar', [Categoria::class, 'all']);
            Route::get('listar/{categoria}', [Categoria::class, 'show']);
            Route::post('crear', [Categoria::class, 'store']);
            Route::post('actualizar/{categoria}', [Categoria::class, 'update']);
            Route::delete('eliminar/{categoria}', [Categoria::class, 'destroy']);
        });
        Route::prefix('familias')->group(function () {
            Route::get('/', [Familia::class, 'index'])->name('admin.familia.index');
            Route::get('reportes/excel', [Familia::class, 'reporteExcel']);
            Route::get('reportes/pdf', [Familia::class, 'reportePdf']);
            Route::post('listar', [Familia::class, 'listar']);
            Route::get('listar/{familia}', [Familia::class, 'show']);
            Route::post('crear', [Familia::class, 'store']);
            Route::post('editar/{familia}', [Familia::class, 'update']);
            Route::delete('eliminar/{familia}', [Familia::class, 'destroy']);
            Route::get('subfamilia/eliminar/{subfamilia}', [Familia::class, 'eliminarSubfamilia']);
        });
        Route::prefix('aduaneros')->group(function () {
            Route::get('/', [Aduaneros::class, 'index'])->name('admin.aduaneros.index');
            Route::get('reportes/excel', [Aduaneros::class, 'reporteExcel']);
            Route::get('reportes/pdf', [Aduaneros::class, 'reportePdf']);
            Route::post('listar', [Aduaneros::class, 'listar']);
            Route::get('listar/{aduanero}', [Aduaneros::class, 'show']);
            Route::post('crear', [Aduaneros::class, 'store']);
            Route::post('editar/{aduanero}', [Aduaneros::class, 'update']);
            Route::delete('eliminar/{aduanero}', [Aduaneros::class, 'destroy']);
        });
        Route::prefix('producto')->group(function () {
            Route::get('/', [MisProductos::class, 'index'])->name('admin.producto.index');
            Route::get('reportes/excel', [MisProductos::class, 'reporteExcel']);
            Route::get('reportes/pdf', [MisProductos::class, 'reportePdf']);
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
            Route::get('general/clientes', [Kardex::class, 'indexKardexGeneralCliente'])->name('admin.kardex.general.cliente');
            Route::get('general/clientes/listar', [Kardex::class, 'mostrarClientesKardexGeneral']);
            Route::get('general/clientes/reporte/{tipo}', [Kardex::class, 'mostrarClientesKardexGeneralReporte']);
            Route::put('eliminar/{id}', [Kardex::class, 'eliminarKardex']);
            Route::get('todos', [Kardex::class, 'misKardexIndex'])->name('admin.miskardex.index');
            Route::get('visualizar/adicional/{kardex}', [Kardex::class, 'informacionAdicional']);
            Route::get('facturar/{kardex}', [Kardex::class, 'informacionFacturar']);
            Route::get('facturar/guia-remision/{kardex}', [Kardex::class, 'informacionGuiaRemitente']);
            Route::post('facturar/guia-remision', [Kardex::class, 'facturarGuiaRemision']);
            Route::post('facturar', [Kardex::class, 'facturarKardex']);
            Route::post('actualizar/fardos', [Kardex::class, 'actualizarValoresKardex']);
            Route::post('actualizar/tasa', [Kardex::class, 'actualizarTasas']);
            Route::post('actualizar/categoriza-tasas', [Kardex::class, 'actualizarTasasCategoria']);
            Route::post('actualizar/aduanero', [Kardex::class, 'actualizarAduanero']);
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
            Route::post('pendiente/producto/eliminar', [Kardex::class, 'eliminarProductoFardo']);
            Route::post('pendiente/eliminar', [Kardex::class, 'eliminarFardo']);
            Route::post('pendiente/abrir', [Kardex::class, 'abrirFardo']);
            Route::post('listar', [Kardex::class, 'listar']);
            Route::get('listar/{kardex}', [Kardex::class, 'show']);
            Route::post('crear', [Kardex::class, 'store']);
            Route::post('editar/{kardex}', [Kardex::class, 'update']);
            Route::delete('eliminar/{kardex}', [Kardex::class, 'destroy']);
        });
    });
    Route::prefix('ranking')->group(function () {
        Route::get('clientes', [Ranking::class, 'indexClientes'])->name('admin.ranking.clientes');
        Route::post('clientes/listar', [Ranking::class, 'listarClientes']);
        Route::get('clientes/reportes/{tipo}', [Ranking::class, 'listarClientesExcel']);
        Route::get('proveedores', [Ranking::class, 'indexProveedores'])->name('admin.ranking.proveedores');
        Route::post('proveedores/listar', [Ranking::class, 'listarProveedores']);
        Route::get('proveedores/reportes/{tipo}', [Ranking::class, 'listarProveedoresExcel']);
        Route::get('aduaneros', [Ranking::class, 'indexAduaneros'])->name('admin.ranking.aduaneros');
        Route::post('aduaneros/listar', [Ranking::class, 'precioRankingAduanero']);
        Route::get('aduaneros/reportes/{tipo}', [Ranking::class, 'listarAduanerosExcel']);

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
