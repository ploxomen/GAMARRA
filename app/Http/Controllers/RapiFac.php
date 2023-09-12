<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapiFac extends Controller
{
    private $urlAutenticacionPrueba = "https://wsoauth-exp.rapifac.com/oauth2/token";
    private $urlComprobante = "https://wsventas-exp.rapifac.com/v0/comprobantes?IncluirCDR=1";
    private $urlListaComprobantes = "https://wsventas-exp.rapifac.com/v0/comprobantes";
    private $urlRecuperarComprobante = "https://wsventas-exp.rapifac.com/v0/comprobantes";
    private $urlAnularComprobante = "https://wsventas-exp.rapifac.com/v0/comprobantes/anular?IncluirCDR=1";

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
                "CondicionPago" => "Credito",
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
            return json_decode($data);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getMessage();
        }
    }
    
    function detallesFactura($detalleKardex) {
        $detalles = [];
        $total = 0;
        foreach ($detalleKardex as $key => $kardex) {
            $importe = $kardex->cantidad * $kardex->precio;
            $detalles[] = [
                "Cantidad"=> $kardex->cantidad,
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
        $url = $this->urlRecuperarComprobante . "?Id=" . $idDocumento . "&TipoDocumento=" . $tipoDocumento . "&Serie=" . $serie ."&Correlativo=" . $correlativo .'&Sucursal=' . env('API_RAPIFAC_SUCURSAL_ID') . '&Usuario=' . env('API_RAPIFAC_USER') . '&Detalles=1&Adicionales=1&Movimientos=0';
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
}
