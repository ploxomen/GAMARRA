<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RankingProveedores implements FromView,WithStyles,ShouldAutoSize
{
    private $datos;
    private $inicio = 4;
    private $fin;
    private $fechaInicio;
    private $fechaFin;

    
    function __construct($datos,$fechaFin,$fechaInicio)
    {
        $this->datos = $datos;
        $this->fin = $this->inicio + $datos->count();
        $this->fechaFin = $fechaFin;
        $this->fechaInicio = $fechaInicio;

    }


    public function view(): View
    {
        return view('ranking.export.proveedores', [
            'datos' => $this->datos,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $this->fin++;
        $rango = "B" . $this->inicio . ":D" . $this->fin;
        $tituloPackingList = $sheet->getStyle('B2');
        $tituloPackingList->getFont()->setBold(true);
        $tituloPackingList->getFont()->setUnderline(true);
        $tituloPackingList->getFont()->setSize(14);
        $tituloPackingList->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("B2:D2");
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension($this->inicio)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $sheet->setCellValue('D'.$this->fin, '=SUM(E'. ($this->inicio + 1).':D'.($this->fin - 1) . ')');
        $sheet->getStyle("B".$this->fin . ':' . "D" . $this->fin)->getFont()->setBold(true);
    }
}
