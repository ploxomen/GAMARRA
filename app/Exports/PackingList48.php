<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class PackingList48 implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    protected $kardex;
    protected $filaInicial = 8;
    protected $totalCantidad;
    protected $totalKilaje;
    protected $filaFinal;
    public function __construct(array $kardex,float $totalCantidad, float $totalKilajes, int $filaFinal){
        $this->kardex = $kardex;
        $this->totalCantidad = $totalCantidad;
        $this->totalKilaje = $totalKilajes;
        $this->filaFinal = $filaFinal;
    }
    public function view(): View
    {
        return view('kardex.reportesExcel.packingList48', [
            'kardex' => $this->kardex,
            'kilajes' => $this->totalKilaje,
            'cantidades' => $this->totalCantidad
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $this->filaFinal++;
        $rango = "A" . $this->filaInicial . ":E" . $this->filaFinal;
        $tituloPackingList = $sheet->getStyle('A2');
        $tituloPackingList->getFont()->setBold(true);
        $tituloPackingList->getFont()->setUnderline(true);
        $tituloPackingList->getFont()->setSize(22);
        $tituloPackingList->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("A8:E8");
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension($this->filaInicial)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $sheet->setCellValue('B5', '=COUNT(A'.($this->filaInicial + 1) . ':A'. ($this->filaFinal - 1) . ')');
        $sheet->setCellValue('B'.$this->filaFinal, '=SUM(B'. ($this->filaInicial + 1).':B'.($this->filaFinal - 1) . ')');
        $sheet->setCellValue('E'.$this->filaFinal, '=SUM(E'. ($this->filaInicial + 1).':E'.($this->filaFinal - 1) . ')');
        $sheet->getStyle("A".$this->filaFinal . ':' . "E" . $this->filaFinal)->getFont()->setBold(true);
    }
    public function title(): string
    {
        return 'PACKING LIST 48';
    }
}
