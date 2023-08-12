<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PackingList implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    protected $kardex;
    protected $filaInicial = 7;
    protected $totalCantidad;
    protected $totalKilaje;
    protected $filaFinal;
    public function __construct(array $kardex,int $filaFinal){
        $this->kardex = $kardex;
        // dd($this->kardex);
        $this->filaFinal = $filaFinal;
    }
    public function view(): View
    {
        return view('kardex.reportesExcel.packingList', [
            'kardex' => $this->kardex
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $this->filaFinal++;
        $rango = "A" . $this->filaInicial . ":K" . $this->filaFinal;
        $tituloPackingList = $sheet->getStyle('A2');
        $tituloPackingList->getFont()->setBold(true);
        $tituloPackingList->getFont()->setUnderline(true);
        $tituloPackingList->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("A".$this->filaInicial . ':' . "K" . $this->filaInicial);
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension($this->filaInicial)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $filaFormula = $this->filaInicial + 1;
        foreach ($this->kardex as $valor) {
            $sheet->setCellValue('I'.$filaFormula, '=G'.$filaFormula.'*H'.$filaFormula);
            $sheet->setCellValue('J'.$filaFormula, '=G'.$filaFormula.'*'.$valor['tasa_extranjera']);
            $sheet->setCellValue('K'.$filaFormula, '=I'.$filaFormula.'-J'.$filaFormula);
            $filaFormula += $valor['totalProductos'];
        }
        $filaFormula = $this->filaInicial + 1;
        $sheet->setCellValue('D'.$this->filaFinal, '=SUM(D'.$this->filaInicial + 1 .':D'.$this->filaFinal - 1 .')');
        $sheet->setCellValue('G'.$this->filaFinal, '=SUM(G'.$this->filaInicial + 1 .':G'.$this->filaFinal - 1 .')');
        $sheet->setCellValue('I'.$this->filaFinal, '=SUM(I'.$this->filaInicial + 1 .':I'.$this->filaFinal - 1 .')');
        $sheet->setCellValue('J'.$this->filaFinal, '=SUM(J'.$this->filaInicial + 1 .':J'.$this->filaFinal - 1 .')');
        $sheet->setCellValue('K'.$this->filaFinal, '=SUM(K'.$this->filaInicial + 1 .':K'.$this->filaFinal - 1 .')');
        $sheet->getStyle("A".$this->filaFinal . ':' . "K" . $this->filaFinal)->getFont()->setBold(true);
        $sheet->getStyle('H' . $filaFormula . ':K' .$this->filaFinal)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
    }
    public function title(): string
    {
        return 'PACKING LIST CLIENTES';
    }
}
