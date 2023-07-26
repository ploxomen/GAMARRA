<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KardexExport implements WithMultipleSheets
{
    protected $kardex;
    protected $filaInicial = 8;
    protected $totalCantidad;
    protected $totalKilaje;
    protected $filaFinal;

    public function __construct(object $kardex)
    {
        $totalCantidad = 0;
        $totalKilajes = 0;
        $nroFilas = $this->filaInicial;
        $kardexLista = [];
        foreach ($kardex->fardos as $fardo){
            $fardos = [
                'numero' => $fardo->nro_fardo,
                'kilaje' => $fardo->kilaje,
                'cliente' => $fardo->clientes->nombreCliente,
                'tasa' => $fardo->tasa,
                'tasa_extranjera' => $fardo->tasa_extranjera
            ];
            $totalKilajes += $fardo->kilaje;
            foreach ($fardo->productosDetalle as $detalle){
                $totalCantidad += $detalle->cantidad;
                $nroFilas++;
                $fardos['detalles'][] = [
                    'cantidad' => $detalle->cantidad,
                    'nombreProducto' => $detalle->productos->nombreProducto,
                    'presentacion' => $detalle->presentaciones->presentacion
                ];
            }
            $kardexLista[] = $fardos;
        }
        $this->kardex = $kardexLista;
        $this->totalCantidad = $totalCantidad;
        $this->totalKilaje = $totalKilajes;
        $this->filaFinal = $nroFilas;
    }
    public function sheets(): array
    {
        $sheets = [
            new PackingList48($this->kardex,$this->totalCantidad,$this->totalKilaje,$this->filaFinal),
            new PackingList($this->kardex,$this->totalCantidad,$this->totalKilaje,$this->filaFinal)
        ];
        return $sheets;
    }
}
