<?php

namespace App\Exports;

use App\Models\KardexFardoDetalle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PreFacturacion implements WithMultipleSheets
{
    protected $kardex;

    public function __construct(object $kardex)
    {
        $datos = [];
        $clientes = $kardex->fardos()->selectRaw('id,id_cliente,GROUP_CONCAT(id) AS idFardos')->groupBy("id_cliente")->get();
        foreach ($clientes as $cliente) {
            $clienteDato = [
                'ruc' => $cliente->clientes->usuario->nroDocumento,
                'nombre' => $cliente->clientes->nombreCliente,
            ];
            $explodeIdFardos = explode(',',$cliente->idFardos);
            $detalleFardos = KardexFardoDetalle::selectRaw('id_producto,precio,SUM(cantidad) AS cantidad')->whereIn('id_fardo',$explodeIdFardos)->groupBy("id_producto")->get();
            foreach ($detalleFardos as $detalle) {
                $clienteDato['productos'][] = [
                    'cantidad' => $detalle->cantidad,
                    'descripcion' => $detalle->productos->nombreProducto,
                    'precio' => $detalle->precio
                ];
            }
            $datos[] = $clienteDato;
        }
        $this->kardex = $datos;
    }
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->kardex as $kar) {
            $sheets[] = new PreFacturacionCliente($kar,count($kar['productos']));
        }
        return $sheets;
    }
}
