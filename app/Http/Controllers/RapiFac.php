<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapiFac extends Controller
{
    private $urlRapifacAutenticacion = "https://wsoauth-exp.rapifac.com";
    private $urlRapifacEmision = "https://wsventas-exp.rapifac.com";
    private $urlRapifacComprobantes = "https://wscomprobante-exp.rapifac.com";
    private $urlAutenticacionPrueba;
    private $urlComprobante;
    private $urlListaComprobantes;
    private $urlAnularComprobante;
    public $urlPdfComprobantes;
    public function __construct() {
        if(env('API_RAPIFAC_PRODUCTION') === 'true'){
            $this->urlRapifacAutenticacion = 'https://wsoauth.rapifac.com';
            $this->urlRapifacEmision = 'https://wsventas.rapifac.com';
            $this->urlRapifacComprobantes = 'https://wscomprobante.rapifac.com';
        }
        $this->urlAutenticacionPrueba = $this->urlRapifacAutenticacion . '/oauth2/token';
        $this->urlComprobante = $this->urlRapifacEmision . '/v0/comprobantes?IncluirCDR=1';
        $this->urlListaComprobantes = $this->urlRapifacEmision . '/v0/comprobantes';
        $this->urlAnularComprobante = $this->urlRapifacEmision . '/v0/comprobantes/anular?IncluirCDR=1';
        $this->urlPdfComprobantes = $this->urlRapifacComprobantes . '/v0/comprobantes/pdf';
    }
    function obtenerToken()  {
        $cliente = new Client();
        $cabeceras = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $parametros = [
              'username' => env('API_RAPIFAC_USERNAME'),
              'password' => env('API_RAPIFAC_PASSWORD'),
              'client_id' => env('API_RAPIFAC_CLIENT_ID'),
              'grant_type' => 'password'
        ];
        $response = $cliente->get($this->urlAutenticacionPrueba,[
            'headers' => $cabeceras,
            'form_params' => $parametros
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function refrescarToken($token){
        $cliente = new Client();
        $cabeceras = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $parametros = [
            'client_id' => env('API_RAPIFAC_CLIENT_ID'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token
        ];
        $response = $cliente->get($this->urlAutenticacionPrueba,[
            'headers' => $cabeceras,
            'form_params' => $parametros
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function listaDetallesGuiaRemision($detalleKardex){
        $detalles = [];
        foreach ($detalleKardex as $key => $kardex) {
            $detalles[] = [
                "Cantidad" => $kardex->cantidad,
                "CantidadReferencial" => $kardex->cantidad,
                "CantidadUnidadMedida" => 1,
                "Cargo" => 0,
                "CargoCargoCodigo" => "",
                "CargoIndicador" => 0,
                "CargoItem" => 0,
                "CargoNeto" => 0,
                "CargoPorcentaje" => 0,
                "CargoTotal" => 0,
                "CodigoCategoria" => 0,
                "ComprobanteID" => 0,
                "Control" => 0,
                "Descripcion" => $kardex->productos->nombreProducto,
                "Descuento" => 0,
                "DescuentoBase" => 0,
                "DescuentoCargo" => 0,
                "DescuentoCargoCodigo" => "00",
                "DescuentoCargoGravado" => 0,
                "DescuentoGlobal" => 0,
                "DescuentoIndicador" => 0,
                "DescuentoMonto" => 0,
                "DescuentoPorcentaje" => 0,
                "EsAnticipo" => false,
                "Ganancia" => 0,
                "ICBPER" => 0,
                "ICBPERItem" => 0,
                "ICBPERSubTotal" => 0,
                "ID" => 0,
                "IGV" => 0,
                "IGVNeto" => 0,
                "ISC" => 0,
                "ISCMonto" => 0,
                "ISCNeto" => 0,
                "ISCPorcentaje" => 0,
                "ISCUnitario" => 0,
                "Importado" => false,
                "ImporteTotal" => 0,
                "ImporteTotalReferencia" => 1,
                "Item" => $key + 1,
                "MontoTributo" => 0,
                "Observacion" => "",
                "Peso" => 0,
                "PesoTotal" => 0,
                "Peso_BASE" => 0,
                "PrecioUnitario" => 0,
                "PrecioUnitarioItem" => 0,
                "PrecioUnitarioNeto" => 0,
                "PrecioVenta" => 0,
                "PrecioVentaCodigo" => "01",
                "ProductoCodigo" => $kardex->productos->codigo,
                "ProductoCodigoCliente" => $kardex->productos->codigo,
                "ProductoCodigoSUNAT" => "",
                "TipoAfectacionIGVCodigo" => "10",
                "TipoProductoCodigo" => "1",
                "TipoSistemaISCCodigo" => "00",
                "UnidadMedidaCodigo" => $kardex->id_presentacion,
                "ValorUnitario" => 0,
                "ValorUnitarioNeto" => 0,
                "ValorVenta" => 0,
                "ValorVentaItem" => 0,
                "ValorVentaItemXML" => 0,
                "ValorVentaNeto" => 0,
                "ValorVentaNetoXML" => 0
            ];
        }
        return $detalles;
    }
    function facturar($productos,$datosFactura){
        $detalle = $this->detallesFactura($productos);
        list($detallesFacturacion,$montoTotal) = $detalle;
        $parametros = [
                "CargoGlobalMonto" => 0,
                "CargoGlobalMontoBase" => $montoTotal,
                "ClienteDireccion" => $datosFactura['ClienteDireccion'],
                "ClienteNombreRazonSocial" => $datosFactura['ClienteNombreRazonSocial'],
                "ClienteNumeroDocIdentidad" => $datosFactura['ClienteNumeroDocIdentidad'],
                "ClientePaisDocEmisor" => "US",
                "ClienteTipoDocIdentidadCodigo" => $datosFactura['ClienteTipoDocIdentidadCodigo'],
                "CondicionPago" => $datosFactura['CondicionPago'],
                "Correlativo" => 2999,
                "CorrelativoModificado" => "",
                "CorreoElectronicoPrincipal" => "jeanpi.jpct@gmail.com",
                "CreditoTotal" => $datosFactura['CreditoTotal'] ? $montoTotal : 0,
                "DescuentoGlobal" => 0,
                "DescuentoGlobalMontoBase" => 0,
                "DescuentoGlobalNGMonto" => 0,
                "DescuentoGlobalNGMontoBase" => $montoTotal,
                "DescuentoGlobalPorcentaje" => 0,
                "DescuentoGlobalValor" => 0,
                "Exonerada" => 0,
                "ExoneradaXML" => 0,
                "Exportacion" => $montoTotal,
                "ExportacionXML" => $montoTotal,
                "FechaConsumo" => $datosFactura['FechaEmision'],
                "FechaEmision" => $datosFactura['FechaEmision'],
                "FechaIngresoEstablecimiento" => $datosFactura['FechaEmision'],
                "FechaIngresoPais" => $datosFactura['FechaEmision'],
                "Gratuito" => 0,
                "GratuitoGravado" => 0,
                "Gravado" => 0,
                "ICBPER" => 0,
                "ID" => 0,
                "IGV" => 0,
                "IGVPorcentaje" => 18,
                "ISC" => 0,
                "ISCBase" => 0,
                "IdRepositorio" => 0,
                "ImporteTotalTexto" => $this->numeroAPalabras($montoTotal),
                "ImpuestoTotal" => 0,
                "ImpuestoVarios" => 0,
                "Inafecto" => 0,
                "InafectoXML" => 0,
                "ListaDetalles" => $detallesFacturacion,
                "ListaMovimientos" => [],
                "MonedaCodigo" => "USD",
                "Observacion" => $datosFactura['Observacion'],
                "OperacionNoGravada" => $montoTotal,
                "OrigenSistema" => 0,
                "PendientePago" => number_format($montoTotal,2),
                "Serie" => "E001",
                "SerieModificado" => "",
                "Sucursal" => env('API_RAPIFAC_SUCURSAL_ID'),
                "TipoCambio" => "3.919",
                "TipoDocumentoCodigo" => "01",
                "TipoDocumentoCodigoModificado" => "01",
                "TipoNotaCreditoCodigo" => "01",
                "TipoNotaDebitoCodigo" => "01",
                "TipoOperacionCodigo" => "0200",
                "TotalAnticipos" => 0,
                "TotalCuotas" => 0,
                "TotalDescuentos" => 0,
                "TotalImporteVenta" => $montoTotal,
                "TotalImporteVentaCelular" => $montoTotal,
                "TotalImporteVentaReferencia" => 0,
                "TotalOtrosCargos" => 0,
                "TotalPago" => $montoTotal,
                "TotalPrecioVenta" => $montoTotal,
                "TotalRetencion" => 0,
                "TotalValorVenta" => $montoTotal,
                "Ubigeo" => "",
                "Usuario" => env('API_RAPIFAC_USER'),
                "Vendedor" => env('API_RAPIFAC_USER'),
                "VendedorNombre" => Auth::user()->nombres
        ];
        if(isset($datosFactura['ListaCuotas'])){
            $parametros['ListaCuotas'] = $datosFactura['ListaCuotas'];
            $parametros['PermitirCuotas'] = count($datosFactura['ListaCuotas']);
        }
        if(isset($datosFactura['ListaGuias'])){
            $parametros['ListaGuias'] = $datosFactura['ListaGuias'];
        }
        try {
            $token = $this->obtenerToken();
            $client = new Client();
            $headers = [
                'Authorization' => 'bearer ' . $token->access_token,
                'Content-Type' => 'application/json'
            ];
            $body = json_encode($parametros);
            $response = $client->post($this->urlComprobante,[
                'headers' => $headers,
                'body' => $body
            ]);
            $data = $response->getBody()->getContents();
            $nuevaData = json_decode($data);
            $nuevaData->MontoTotal = $montoTotal;
            return $nuevaData;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getMessage();
        }
    }
    function detallesFactura($detalleKardex) {
        $detalles = [];
        $total = 0;
        foreach ($detalleKardex as $key => $kardex) {
            $importe = $kardex->totalCantidades * $kardex->precio;
            $detalles[] = [
                "Cantidad"=> $kardex->totalCantidades,
                "CantidadUnidadMedida"=> 1,
                "Cargo"=> 0,
                "CargoCargoCodigo"=> 0,
                "CargoIndicador"=> 0,
                "CargoItem"=> 0,
                "CargoNeto"=> 0,
                "CargoPorcentaje"=> 0,
                "CargoTotal"=> 0,
                "CodigoCategoria"=> 0,
                "ComprobanteID"=> 0,
                "Descripcion"=> $kardex->productos->nombreProducto,
                "Descuento"=> 0,
                "DescuentoBase"=> $importe,
                "DescuentoCargoCodigo"=> "01",
                "DescuentoIndicador"=> 1,
                "DescuentoMonto"=> 0,
                "DescuentoPorcentaje"=> 0,
                "ICBPER"=> 0,
                "ICBPERItem"=> 0,
                "ICBPERSubTotal"=> 0,
                "ID"=> 0,
                "IGV"=> 0,
                "IGVNeto"=> 0,
                "ISC"=> 0,
                "ISCMonto"=> 0,
                "ISCNeto"=> 0,
                "ISCPorcentaje"=> 0,
                "ISCUnitario"=> 0,
                "ImporteTotal"=> $importe,
                "Item"=> $key + 1,
                "MontoTributo"=> 0,
                "Observacion"=> "",
                "PrecioUnitario"=> $kardex->precio,
                "PrecioUnitarioItem"=> $kardex->precio,
                "PrecioUnitarioNeto"=> $kardex->precio,
                "PrecioVenta"=> $importe,
                "PrecioVentaCodigo"=> "01",
                "ProductoCodigo"=> $kardex->productos->codigo,
                "ProductoCodigoSUNAT"=> "",
                "TipoAfectacionIGVCodigo"=> "40",
                "TipoProductoCodigo"=> "",
                "TipoSistemaISCCodigo"=> "00",
                "UnidadMedidaCodigo"=> $kardex->id_presentacion,
                "ValorUnitario"=> $kardex->precio,
                "ValorUnitarioNeto"=> $kardex->precio,
                "ValorVenta"=> $importe,
                "ValorVentaItem"=> $importe,
                "ValorVentaItemXML"=> $importe,
                "ValorVentaNeto"=> $importe,
                "ValorVentaNetoXML"=> $importe
            ];
            $total += $importe;
        }
        return [$detalles,$total];
    }
    function numeroAPalabras($numero) {
        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        // Convierte el número en palabras
        $palabras = $fmt->format($numero);
        if(strpos($palabras,"coma") !== false){
            $palabras = substr($palabras,0,strpos($palabras,"coma") - 1);
        }
        // Si hay decimales, agregarlos
        $decimal = "00";
        if (strpos($numero, '.') !== false) {
            list($entero, $decimal) = explode('.', $numero);
            $decimal = ltrim($decimal, '0'); // Eliminar ceros a la izquierda
        }
        $palabras .= ' Y ' . $decimal . '/100 DOLARES';
        return mb_strtoupper($palabras,'UTF-8');
    }
    function listarComprobantes($documento = "00",$desde,$hasta,$busqueda="",$pagina) {
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token
        ];
        $urlPost = $this->urlListaComprobantes . "?documento=" . $documento . "&desde=" . date('d/m/Y',strtotime($desde)) . '&hasta='. date('d/m/Y',strtotime($hasta)) . '&busqueda='.$busqueda . '&pagina='. $pagina .'&sucursal=' . env('API_RAPIFAC_SUCURSAL_ID') . '&usuario=' . env('API_RAPIFAC_USER');
        $response = $client->get($urlPost,[
            'headers' => $headers,
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    function recuperarComprobante($idDocumento,$tipoDocumento,$serie,$correlativo){
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $parametros = [
            'Id' => $idDocumento,
            'TipoDocumento' => $tipoDocumento,
            'Serie' =>  $serie,
            'Correlativo' => $correlativo,
            'Sucursal' => env('API_RAPIFAC_SUCURSAL_ID'),
            'Usuario' =>  env('API_RAPIFAC_USER'),
        ];
        $body = json_encode($parametros);
        $url = $this->urlListaComprobantes . "?Id=" . $idDocumento . "&TipoDocumento=" . $tipoDocumento . "&Serie=" . $serie ."&Correlativo=" . $correlativo .'&Sucursal=' . env('API_RAPIFAC_SUCURSAL_ID') . '&Usuario=' . env('API_RAPIFAC_USER') . '&Detalles=1&Adicionales=1&Movimientos=0';
        $response = $client->get($url,[
            'headers' => $headers,
        ]);
        $data = $response->getBody()->getContents();
        dd(json_decode($data));
        
    }
    function anularComprobante($idDocumento,$tipoDocumento,$serie,$correlativo,$motivoBaja,$fecha) {
        $client = new Client();
        $token = $this->obtenerToken();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $parametros = [
            'Id' => $idDocumento,
            'TipoDocumentoCodigo' => $tipoDocumento,
            'Serie' =>  $serie,
            'Correlativo' => $correlativo,
            'FechaEmision' => $fecha,
            'MotivoBaja' => empty($motivoBaja) ? "Error en el registro de la factura" : $motivoBaja,
            "ListaDetalles" => [],
            "ListaMovimientos" => []
        ];
        $body = json_encode($parametros);
        try {
            $response = $client->put($this->urlAnularComprobante,[
                'headers' => $headers,
                'body' => $body
            ]);
            return ['success' => $response->getBody()->getContents()];
        } catch (\Throwable $th) {
            return ['error' => json_decode(explode("\n",$th->getMessage())[1])];
        }
    }
    function generarGuiaRemision($datosFactura,$productos){
        $detallesFacturacion = $this->listaDetallesGuiaRemision($productos);
        $parametros = [
            "AgentePercepcion" => false,
            "AlojamientoNombreRazonSocial" => "",
            "AlojamientoNumeroDocIdentidad" => "",
            "AlojamientoPaisDocEmisor" =>  "AF",
            "AlojamientoTipoDocIdentidadCodigo" => "1",
            "BienServicioCodigo" => "027",
            "Bultos" => count($detallesFacturacion),
            "BultosCelular" => count($detallesFacturacion),
            "ClienteDireccion" => "-",
            "ClienteNombreRazonSocial" => $datosFactura['ClienteNombreRazonSocial'],
            "ClienteNumeroDocIdentidad" => $datosFactura['ClienteNumeroDocIdentidad'],
            "ClientePaisDocEmisor" => "PE",
            "ClienteTelefono" => "",
            "ClienteTipoDocIdentidadCodigo" => "6",
            "ClienteTipoSunat" => 1,
            "ClienteUbigeo" => "",
            "ConductorLicencia" => $datosFactura['ConductorLicencia'],
            "ConductorLicencia2" => $datosFactura['ConductorLicencia2'],
            "ConductorNumeroDocIdentidad" => $datosFactura['ConductorNumeroDocIdentidad'],
            "ConductorTipoDocIdentidadCodigo" => $datosFactura['ConductorTipoDocIdentidadCodigo'],
            "Correlativo" => 144,
            "CorreoElectronicoPrincipal" => "no-send@rapifac.com",
            "CreditoTotal" => 0,
            "DUADAMCodigo" => "",
            "DescuentoGlobalMonto" => 0,
            "DescuentoNGMonto" => 0,
            "DiasPermanencia" => 0,
            "DireccionLlegada" => $datosFactura['DireccionLlegada'],
            "DireccionPartida" => $datosFactura['DireccionPartida'],
            "DocAdicionalCodigo" => 1,
            "DocAdicionalDetalle" => "",
            "ExoneradaXML" => 0,
            "Exportacion" => 0,
            "ExportacionXML" => 0,
            "FechaConsumo" => $datosFactura['FechaEmision'],
            "FechaEmision" => $datosFactura['FechaEmision'],
            "FechaIngresoEstablecimiento" => $datosFactura['FechaEmision'],
            "FechaIngresoPais" => $datosFactura['FechaEmision'],
            "FechaSalidaEstablecimiento" => "",
            "FechaTraslado" => $datosFactura['FechaTraslado'],
            "FormatoPDF" => 0,
            "GratuitoGravado" => 0,
            "Gravado" => 0,
            "GuiaNumero" => "",
            "ICBPER" => 0,
            "ID" => 0,
            "IGV" => 0,
            "IGVPorcentaje" => 18,
            "ISC" => 0,
            "ISCBase" => 0,
            "IdRepositorio" => 0,
            "ImporteTotalTexto" => "CERO CON 00/100 DOLARES",
            "ImpuestoTotal" => 0,
            "ImpuestoVarios" => 0,
            "Inafecto" => 0,
            "InafectoXML" => 0,
            "ListaDetalles" => $detallesFacturacion,
            "ListaDocumentosRelacionados" => isset($datosFactura['ListaDocumentosRelacionados']) ? $datosFactura['ListaDocumentosRelacionados'] : [],
            "ListaMovimientos" => [],
            "ModalidadTrasladoCodigo" => "01",
            "MonedaCodigo" => "USD",
            "MontoRetencion" => 0,
            "MotivoTrasladoCodigo" => "01",
            "MotivoTrasladoDescripcion" => "VENTA",
            "NOMBRE_UBIGEOLLEGADA" => $datosFactura['NOMBRE_UBIGEOLLEGADA'],
            "NOMBRE_UBIGEOPARTIDA" => $datosFactura['NOMBRE_UBIGEOPARTIDA'],
            "Observacion" => $datosFactura['Observacion'],
            "PaisResidencia" => "AF",
            "PendientePago" => "0.00",
            "PercepcionFactor" => 0,
            "PercepcionRegimen" => "",
            "PercepcionTotal" => 0,
            "PermitirCuotas" => 1,
            "Peso" => 0,
            "PesoTotal" => $datosFactura['PesoTotal'],
            "PesoTotalCelular" => $datosFactura['PesoTotal'],
            "RemitenteNombreRazonSocial" => "",
            "RemitenteNumeroDocIdentidad" => "",
            "RemitenteTipoDocIdentidadCodigo" => "",
            "RetencionPorcentaje" => 0,
            "Serie" => "T002",
            "SerieModificado" => "",
            "SituacionPagoCodigo" => 2,
            "Sucursal" => env('API_RAPIFAC_SUCURSAL_ID'),
            "TipoCambio" => "3.919",
            "TipoDocumentoCodigo" => "09",
            "TipoGuiaRemisionCodigo" => "",
            "TipoOperacionCodigo" => "0101",
            "TotalImporteVenta" => 0,
            "TotalImporteVentaReferencia" => 0,
            "TotalOtrosCargos" => 0,
            "TotalPago" => 0,
            "TotalPrecioVenta" => 0,
            "TotalValorVenta" => 0,
            "TransportistaNombreRazonSocial" => $datosFactura['TransportistaNombreRazonSocial'],
            "TransportistaNombreRazonSocial2" => "",
            "TransportistaNumeroDocIdentidad" => $datosFactura['TransportistaNumeroDocIdentidad'],
            "TransportistaNumeroDocIdentidad2" => "",
            "TransportistaTipoDocIdentidadCodigo" => "6",
            "TransportistaTipoDocIdentidadCodigo2" => "",
            "Ubigeo" => "",
            "UbigeoLlegada" => "",
            "UbigeoPartida" => "",
            "Usuario" => "13131313",
            "VehiculoAutorizacion" => "",
            "VehiculoAutorizacion2" => "",
            "VehiculoCertificado" => $datosFactura['VehiculoCertificado'],
            "VehiculoCertificado2" => $datosFactura['VehiculoCertificado2'],
            "VehiculoConfiguracion" => "",
            "VehiculoConfiguracion2" => "",
            "VehiculoPlaca" => $datosFactura['VehiculoPlaca'],
            "VehiculoPlaca2" => $datosFactura['VehiculoPlaca2'],
            "VehiculoRegistrado" => "",
            "Vendedor" => env('API_RAPIFAC_USER'),
            "VendedorNombre" => $datosFactura['VendedorNombre']
        ];
        $token = $this->obtenerToken();
        $client = new Client();
        $headers = [
            'Authorization' => 'bearer ' . $token->access_token,
            'Content-Type' => 'application/json'
        ];
        $body = json_encode($parametros);
        // dd($body);
        $response = $client->post($this->urlComprobante,[
            'headers' => $headers,
            'body' => $body
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
}
