<?php

namespace App\Http\Controllers;

use App\Exports\KardexExport;
use App\Exports\PreFacturacionCliente;
use App\Models\Aduanero;
use App\Models\Clientes;
use App\Models\Kardex as ModelsKardex;
use App\Models\KardexCliente;
use App\Models\KardexFardo;
use App\Models\KardexFardoDetalle;
use App\Models\KardexProveedor;
use App\Models\Presentacion;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\TipoDocumento;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $productos = Productos::all();
        $clientes = Clientes::all();
        $proveedores = Proveedores::all();
        $presentaciones = Presentacion::orderBy("presentacion")->get();
        return view("kardex.generar",compact("modulos","clientes","productos","proveedores","presentaciones"));
    }
    public function informacionGuiaRemitente(ModelsKardex $kardex) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $detallesProducto = KardexFardoDetalle::obtenerProductosPreFactura($kardex->id);
        foreach ($detallesProducto as $detalleProducto) {
            $detalleProducto->nombreProducto = $detalleProducto->productos->nombreProducto;
        }
        return response()->json(['informacionFactura' => ['listaProductos' => $detallesProducto->setHidden(['id','id_fardo','estado','id_producto','id_proveedor','productos','cantidad']), 'pesoBultoTotal' => $kardex->kilaje, 'observaciones' => 'VAN CONTENIDO ' . KardexFardo::where('id_kardex',$kardex->id)->count() .' FARDOS DE TEXTILES', 'facturaSunat' => $kardex->factura_sunat]]);
    }
    public function facturarGuiaRemision(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $expresionRegular = "/^[A-Za-z]+[0-9]+-[0-9]+$/";
        if($request->has("facturaSunat") && !empty($request->facturaSunat) && !preg_match($expresionRegular, $request->facturaSunat)){
            return response()->json(['error' => 'La factura no tiene el formato solicitado, por favor establesca el formato correcto']);
        }
        $productos = KardexFardoDetalle::obtenerProductosPreFactura($request->kardex);
        $rapifac = new RapiFac();
        $datosFacturar = [
            'ConductorTipoDocIdentidadCodigo' => $request->tipoDocumentoConductorPrincipal,
            'VendedorNombre' => Auth::user()->nombres,
            'ClienteNumeroDocIdentidad' => $request->numeroDocumentoDestinatario,
            'FechaEmision' => date('d/m/Y',strtotime($request->fechaEmision)),
            'TransportistaNumeroDocIdentidad' => $request->numeroDocumentoTransportista,
            'ConductorNumeroDocIdentidad' => $request->numeroDocumentoConductorPrincipal,
            'TransportistaTipoDocIdentidadCodigo2' => "",
            'TransportistaNumeroDocIdentidad2' => "",
            'NOMBRE_UBIGEOLLEGADA' => 'LIMA - CALLAO - CALLAO',
            'NOMBRE_UBIGEOPARTIDA' => 'LIMA - LIMA - LA VICTORIA',
            'BANDERA_TRANSPORTISTA' => $request->nombreTransportista,
            'ClienteDireccion' => $request->direccionDestinatario,
            'ClienteNombreRazonSocial' => $request->nombreDestinatario,
            'DireccionPartida' => $request->puntoPartida,
            'DireccionLlegada' => $request->puntoLlegada,
            'PesoTotal' => $request->pesoBultoTotal,
            'FechaTraslado' => date('d/m/Y',strtotime($request->fechaTraslado)),
            'ConductorLicencia' => $request->numeroLicenciaConductorPrincipal,
            'ConductorLicencia2' => empty($request->numeroLicenciaConductorSecundario) ? "" : $request->numeroLicenciaConductorSecundario,
            'VehiculoPlaca' => $request->numeroPlacaPrincipal,
            'VehiculoPlaca2' => empty($request->numeroPlacaSecundario) ? "" : $request->numeroPlacaSecundario,
            'TransportistaNombreRazonSocial' => $request->nombreTransportista,
            'VehiculoCertificado' => $request->numeroTuceOChvPrincipal,
            'VehiculoCertificado2' => empty($request->numeroTuceOChvSecundario) ? "" : $request->numeroTuceOChvSecundario,
            'ConductorTipoDocIdentidadCodigo2' => empty($request->tipoDocumentoConductorSecundario) ? "" : $request->tipoDocumentoConductorSecundario,
            'ConductorNumeroDocIdentidad2' => empty($request->numeroDocumentoConductorSecundario) ? "" : $request->numeroDocumentoConductorSecundario,
            'Observacion' =>  empty($request->observaciones) ? "" : $request->observaciones
        ];
        $kardex = ModelsKardex::find($request->kardex);
        if($request->has("facturaSunat") && !empty($request->facturaSunat)){
            if(empty($kardex->factura_total_sunat)){
                return response()->json(['error' => 'La factura ' . $request->facturaSunat . ' ingresada, no se encuentra registrada en el sistema, por favor establesca otro número de factura']);
            }
            list($serieRemitente,$correlativoRemitente) = explode('-',$request->facturaSunat);
            $datosFacturar['ListaDocumentosRelacionados'] = [
                [
                    'TipoDocumentoCodigo' => "01",
                    'Moneda' => 'USD',
                    'Serie' => $serieRemitente,
                    'Correlativo' => $correlativoRemitente,
                    'Importe' => $kardex->factura_total_sunat,
                    'Baja' => 0
                ]
            ];
        }
        $generarGuiaRemision = $rapifac->generarGuiaRemision($datosFacturar,$productos);
        // $kardex->update(['estado' => 4,'guia_remision_sunat' => null]);
        // return response()->json(['success' => 'Guia de remision remitente generada correctamente']);
        if(isset($generarGuiaRemision->Mensaje) && empty($generarGuiaRemision->Mensaje)){
            // list($numero,$serie,$correlativo) = explode('-',$generarGuiaRemision->xml_pdf->Mensaje);
            $kardex->update(['estado' => 4]);
            return response()->json(['success' => 'Guia de remision remitente generada correctamente', 'urlPdf' => $rapifac->urlPdfComprobantes .'?key=' . $generarGuiaRemision->IDRepositorio]);
        }
    }
    public function facturarKardex(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $expresionRegular = "/^[A-Za-z]+[0-9]+-[0-9]+$/";
        if($request->has("guiaRemision") && !empty($request->guiaRemision) && !preg_match($expresionRegular, $request->guiaRemision)){
            return response()->json(['error' => 'La guía de remision no tiene el formato solicitado, por favor establesca el formato correcto']);
        }
        $productos = KardexFardoDetalle::obtenerProductosPreFactura($request->kardex);
        $rapifac = new RapiFac();
        $fechaEmison = date('d/m/Y',strtotime($request->fechaEmision));
        $datosFacturar = [
            'ClienteNombreRazonSocial' => $request->nombreAgente,
            'ClienteDireccion' => empty($request->direccionAgente) ? "" : $request->direccionAgente ,
            'ClienteNumeroDocIdentidad' => $request->numeroDocumento,
            'ClienteTipoDocIdentidadCodigo' => $request->agenteTipoDocumento,
            'FechaEmision' => $fechaEmison,
            'CondicionPago' => $request->tipoFactura,
            'Observacion' => empty($request->observaciones) ? "" : $request->observaciones,
            'CreditoTotal' => false
        ];
        if($request->has("guiaRemision") && !empty($request->guiaRemision)){
            list($serieRemitente,$correlativoRemitente) = explode('-',$request->guiaRemision);
            $datosFacturar['ListaGuias'] = [
                [
                    'Serie' => $serieRemitente,
                    'Correlativo' => $correlativoRemitente,
                    'TipoGuia' => 0,
                    'SerieCorrelativo' => $request->guiaRemision
                ]
            ];
        }
        if($request->tipoFactura == 'Credito'){
            $datosFacturar['CreditoTotal'] = true;
            if($request->has('cuotasFacturaFecha') && $request->has('cuotasFacturaMonto')){
                $detalleCuotas = [];
                $fecha1 = new DateTime($request->fechaEmision);
                for ($i=0; $i < count($request->cuotasFacturaFecha); $i++) { 
                    $fecha2 = new DateTime($request->cuotasFacturaFecha[$i]);
                    $direncia = $fecha2->diff($fecha1);
                    $detalleCuotas[] = [
                        'FechaVencimientoCuota' => date('d/m/Y',strtotime($request->cuotasFacturaFecha[$i])),
                        'MontoCuota' => floatval($request->cuotasFacturaMonto[$i]),
                        'PlazoDiasCuota' => $direncia->days
                    ];
                }
                $datosFacturar['ListaCuotas'] = $detalleCuotas;
            }
        }
        
        $generarFactura = $rapifac->facturar($productos,$datosFacturar);
        if(is_null($generarFactura)){
            return response()->json(['error' => 'Error al generar la factura, por favor intentelo nuevamente más tarde']);
        }
        if(isset($generarFactura->xml_pdf) && isset($generarFactura->cdr)){
            $kardex = ModelsKardex::find($request->kardex);
            // Factura::create([
            //     'xml_pdf_numero_documento' => $generarFactura->xml_pdf->Mensaje,
            //     'fecha_emision' => $request->fechaEmision,
            //     'id_kardex' => $request->kardex,
            //     'tipo_documento_destinatario' => $request->agenteTipoDocumento,
            //     'numero_documento_destinatario' => $request->numeroDocumento,
            //     'nombre_completo_destinatario' => $request->nombreAgente,
            //     'xml_pdf_IDComprobante' => $generarFactura->xml_pdf->IDComprobante,
            //     'observaciones' => $request->observaciones,
            //     'xml_pdf_Codigo' => $generarFactura->xml_pdf->Codigo,
            //     'xml_pdf_IDRepositorio' => $generarFactura->xml_pdf->IDRepositorio,
            //     'xml_pdf_Firma' =>  $generarFactura->xml_pdf->Firma,
            //     'cdr_IDComprobante' => $generarFactura->cdr->IDComprobante,
            //     'cdr_Codigo' => $generarFactura->cdr->Codigo,
            //     'cdr_IDRepositorio' => $generarFactura->cdr->IDRepositorio,
            //     'cdr_firma' => $generarFactura->cdr->Firma,
            //     'tipo_factura' => $request->tipoFactura,
            //     'tipo_moneda' => 'USD',
            //     'monto_total' => $kardex->importe,
            //     'estado' => 1
            // ]);
            list($numero,$serie,$correlativo) = explode('-',$generarFactura->xml_pdf->Mensaje);
            $kardex->update(['estado' => 3,'factura_sunat' => $serie . '-'.$correlativo, 'factura_total_sunat' => $generarFactura->MontoTotal,'guia_remision_sunat' => $request->has("guiaRemision") ? $request->guiaRemision : null ]);
            return response()->json(['success' => $generarFactura->cdr->Mensaje, 'urlPdf' => $rapifac->urlPdfComprobantes .'?key=' . $generarFactura->xml_pdf->IDRepositorio]);
        }
    }
    public function informacionFacturar(ModelsKardex $kardex) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $detallesProducto = KardexFardoDetalle::obtenerProductosPreFactura($kardex->id);
        foreach ($detallesProducto as $detalleProducto) {
            $detalleProducto->nombreProducto = $detalleProducto->productos->nombreProducto;
        }
        $rapifac = new RapiFac();
        $kardex->setHidden(['nroFardoActivo','aduanero','id_aduanero','tasa_extranjera','kilaje','estado','fechaActualizada','fechaCreada','factura_sunat','factura_total_sunat']);
        $kardex->agente = $kardex->aduanero->nombre_completo;
        $kardex->agenteTipoDocumento = $kardex->aduanero->tipo_documento;
        $kardex->agenteNumeroDocumento = $kardex->aduanero->nro_documento;
        $kardex->totalLetras = $rapifac->numeroAPalabras($kardex->importe);
        $kardex->listaProductos = $detallesProducto->setHidden(['id','id_fardo','estado','id_producto','id_proveedor','productos','cantidad']);
        return response()->json(['informacionFactura' => $kardex]);
    }
    public function actualizarTasas(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        ModelsKardex::find($request->idKardex)->update(['id_aduanero' => $request->aduanero, 'tasa_extranjera' => $request->tasa_extranjera]);
        if($request->has('cliente') && !empty($request->cliente)){
            $kardexFardo = KardexFardo::where(['id_kardex' => $request->idKardex,'id_cliente' => $request->cliente])->count();
            if(!$kardexFardo){
                return response()->json(['alerta' => 'Tasa extranjera y agente de aduanas modificados correctamente, para actualizar la tasa del cliente se debe asociar al menos un fardo']);
            }
            KardexCliente::where(['id_kardex' => $request->idKardex,'id_cliente' => $request->cliente])->update($request->only("tasa"));
        }
        return response()->json(['success' => 'todos los datos actualizados correctamente']);
    }
    public function actualizarAduanero(Request $request) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        ModelsKardex::find($request->idKardex)->update(['id_aduanero' => $request->aduanero]);
        return response()->json(['success' => 'agente de aduanas actualizado correctamente']);
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
        DB::beginTransaction();
        try {
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
            if($columnaActualizar == 'id_proveedor'){
                $this->filtrarProveedores($kardex->id);
            }
            if($columnaActualizar == 'cantidad' || $columnaActualizar == 'kilaje' || $columnaActualizar == 'precio'){
                list($totalImporte,$cantidad,$kilaje) = $this->calcularImporteKardex($kardex->id);
                $kardex->update(['nroFardoActivo' => null,'cantidad' => $cantidad,'kilaje' => $kilaje,'importe' => $totalImporte]);
            }
            DB::commit();
            return response()->json(['success' => $db ? $request->campo . ' se modifico de manera correcta' : $request->campo . ' no se a podido modificar']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
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
        return Excel::download(new PreFacturacionCliente($kardex), 'prefacturacion_clientes_'. str_pad($kardex->id,5,'0',STR_PAD_LEFT). '.xlsx');
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
        return response()->json(['kardex' => ModelsKardex::verKardexPorTerminar($cliente,$kardex),'tasas' => KardexCliente::select("tasa")->where(['id_cliente' => $cliente,'id_kardex' => $kardex])->first()]);
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
        $kardexCliente = KardexCliente::where(['id_kardex' => $kardex->id,'id_cliente' => $cliente])->first();
        return Pdf::loadView("kardex.reportesPdf.kardexCliente",compact("fardos","kardexCliente"))->stream("kardex_cliente_" . $kardex->id . '_' . $cliente .'.pdf');
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
            return response()->json(['alerta' => 'No hay fardos pendientes por cerrar','fardo' => true]);
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
        DB::beginTransaction();
        try {
            KardexFardoDetalle::where(['id_fardo' => $fardoKardex->id])->delete();
            KardexFardo::where(['id_kardex' => $kardex->id,'nro_fardo' => $request->fardo,'id_cliente' => $request->cliente])->delete();
            foreach (KardexFardo::where(['id_kardex' => $kardex->id])->get() as $k => $v) {
                $v->update(['nro_fardo' => $k + 1]);
            }
            $kardexFardo = KardexFardo::where(['id_kardex' => $kardex->id,'id_cliente' => $request->cliente])->count();
            if(!$kardexFardo){
                KardexCliente::where(['id_kardex' => $kardex->id,'id_cliente' => $request->cliente])->delete();
            }
            $this->filtrarProveedores($kardex->id);
            list($totalImporte,$cantidad,$kilaje) = $this->calcularImporteKardex($kardex->id);
            $kardex->update(['nroFardoActivo' => null,'cantidad' => $cantidad,'kilaje' => $kilaje,'importe' => $totalImporte]);
            DB::commit();
            return response()->json(['success' => 'El fardo N° ' . $request->fardo . ' a sido eliminado correctamente','nroFardo' => $kardex->nroFardoActivo]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    function filtrarProveedores($idKardex){
        $proveedores = KardexFardoDetalle::obtenerProveedoresKardex($idKardex);
        $listaProveedores = [];
        $listaProveedoresClientes = [];
        foreach ($proveedores as $proveedor) {
            if(!in_array($proveedor->id_proveedor,$listaProveedores)){
                $listaProveedores[] = $proveedor->id_proveedor; 
            }
            $filtro = array_filter($listaProveedoresClientes,function($valor)use($proveedor){
                return $valor['id_proveedor'] === $proveedor->id_proveedor;
            });
            if(empty($filtro)){
                $listaProveedoresClientes[] = [
                    'id_proveedor' => $proveedor->id_proveedor,
                    'id_clientes' => [$proveedor->id_cliente]
                ];
                continue;
            }
            if(!in_array($proveedor->id_cliente,$listaProveedoresClientes[key($filtro)]['id_clientes'])){
                $listaProveedoresClientes[key($filtro)]['id_clientes'][] = $proveedor->id_cliente;
            }
        }
        KardexProveedor::where('id_kardex',$idKardex)->whereNotIn('id_proveedores',$listaProveedores)->delete();
        foreach ($listaProveedoresClientes as $cliente) {
            KardexProveedor::where(['id_kardex' => $idKardex,'id_proveedores' => $cliente['id_proveedor']])->whereNotIn('id_cliente',$cliente['id_clientes'])->delete();
        }
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
    public function generarKardex(){
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
            list($totalImporte,$cantidad,$kilaje) = $this->calcularImporteKardex($kardex->id);
            $kardex->update(['nroFardoActivo' => null,'estado' => 2,'cantidad' => $cantidad,'kilaje' => $kilaje,'importe' => $totalImporte]);
            DB::commit();
            return response()->json(['success' => 'El kardex se generó correctamente']);
        }catch(\Exception $th){
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    public function calcularImporteKardex($idKardex) {
        $fardos = KardexFardo::where(['estado' => 2,'id_kardex' => $idKardex])->get();
        $cantidad = KardexFardo::where(['id_kardex' => $idKardex])->sum('cantidad');
        $kilaje = KardexFardo::where(['id_kardex' => $idKardex])->sum('kilaje');
        $totalImporte = 0;
        foreach ($fardos as $fardo) {
            $detalleFardos = KardexFardoDetalle::where(['id_fardo' => $fardo->id])->get();
            foreach ($detalleFardos as $detalleFardo) {
                $totalImporte += $detalleFardo->cantidad * $detalleFardo->precio;
            }
        }
        return [$totalImporte,$cantidad,$kilaje];
    }
    public function misKardex()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        $kardex = ModelsKardex::misKardex();
        return DataTables::of($kardex)->toJson();
    }
    public function eliminarKardex($id) {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return response()->json(['session' => true]);
        }
        ModelsKardex::find($id)->update(['estado' => 0, 'nroFardoActivo' => null]);
        return response()->json(['success' => 'Kardex eliminado correctamente']);
    }
    public function misKardexIndex()  {
        $verif = $this->usuarioController->validarXmlHttpRequest($this->moduloMisKardex);
        if(isset($verif['session'])){
            return redirect()->route("home"); 
        }
        $modulos = $this->usuarioController->obtenerModulos();
        $productos = Productos::all();
        $clientes = Clientes::all();
        $proveedores = Proveedores::all();
        $presentaciones = Presentacion::orderBy("presentacion")->get();
        $aduaneros = Aduanero::where('estado',1)->get();
        $hoy = date('Y-m-d');
        $haceTresDias = date('Y-m-d',strtotime(date('Y-m-d') . ' - 3 days'));
        $tiposDocumentos = TipoDocumento::where('estado',1)->get();
        return view("kardex.misKardex",compact("tiposDocumentos","modulos","productos","clientes","proveedores","presentaciones","aduaneros","hoy","haceTresDias"));
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
                'estado' => 1,
                'id_aduanero' => $aduanero->id,
                'tasa_extranjera' => $aduanero->tasa
            ]);
        }
        $kardexCliente = KardexCliente::where(['id_kardex' => $kardex->id,'id_cliente' => $request->cliente]);
        $cliente = Clientes::find($request->cliente);
        if(!$kardexCliente->count()){
            KardexCliente::create(['id_kardex' => $kardex->id,'id_cliente' => $request->cliente,'tasa' => $cliente->tasa]);
        }
        $nroFardo = $kardex->nroFardoActivo;
        if(empty($nroFardo)){
            $nroFardo = KardexFardo::where('id_kardex',$kardex->id)->count() + 1;
            KardexFardo::create([
                'id_cliente' => $cliente->id,
                'id_kardex' => $kardex->id,
                'nro_fardo' => $nroFardo,
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
            'id_proveedores' => $request->proveedor,
            'id_cliente' => $cliente->id
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
            'precioProducto' => $producto->precioVenta,
            'tipo' => true
        ];
        if(!$request->has('idKardex')){
            $response['precioProducto'] = '';
            $response['tipo'] = false;
        }
        return response()->json($response);
    }
}
