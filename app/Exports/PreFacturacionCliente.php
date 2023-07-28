<?php

namespace App\Exports;

use App\Models\KardexFardoDetalle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PreFacturacionCliente implements FromView,ShouldAutoSize,WithStyles
{
    protected $kardex;
    protected $filaInicial = 4;
    protected $filaFinal;
    public function __construct(object $kardex){
        $datos = [];
        $filaFinal = $this->filaInicial;
        $productos = KardexFardoDetalle::obtenerProductosPreFactura($kardex->id);
        foreach ($productos as $producto) {
            $filaFinal++;
            $datos[] = [
                'cantidad' => $producto->totalCantidades,
                'descripcion' => $producto->productos->nombreProducto,
                'precio' => $producto->precio
            ];
        }
        $this->filaFinal = $filaFinal;
        $this->kardex = $datos;
    }
    public function view(): View
    {
        return view('kardex.reportesExcel.preFacturacionCliente', [
            'kardex' => $this->kardex
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $this->filaFinal++;
        $rango = "A" . $this->filaInicial . ":E" . $this->filaFinal;
        $cabeceraTabla = $sheet->getStyle("A".$this->filaInicial . ':' . "E" . $this->filaInicial);
        $cabeceraTabla->getFont()->setBold(true);
        $cabeceraTabla->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle("B" . $this->filaInicial + 1 . ":B" .  $this->filaFinal)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $filaFormula = $this->filaInicial + 1;
        foreach ($this->kardex as $valor) {
            $sheet->setCellValue('E'.$filaFormula, '=D'.$filaFormula.'*B'.$filaFormula);
            $filaFormula++;
        }
        $filaFormula = $this->filaInicial + 1;
        $sheet->setCellValue('B'.$this->filaFinal, '=SUM(B'.$this->filaInicial + 1 .':B'.$this->filaFinal - 1 .')');
        $sheet->setCellValue('E'.$this->filaFinal, '=SUM(E'.$this->filaInicial + 1 .':E'.$this->filaFinal - 1 .')');
        $sheet->getStyle('D' . $filaFormula . ':E' .$this->filaFinal)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
    }
}
