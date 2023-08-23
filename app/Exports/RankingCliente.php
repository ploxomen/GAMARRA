<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RankingCliente implements FromView,WithStyles,ShouldAutoSize
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
        return view('ranking.export.clientes', [
            'datos' => $this->datos,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $this->fin++;
        $rango = "B" . $this->inicio . ":E" . $this->fin;
        $tituloPackingList = $sheet->getStyle('B2');
        $sheet->getRowDimension(1)->setRowHeight(20);
        $tituloPackingList->getFont()->setBold(true);
        $tituloPackingList->getFont()->setUnderline(true);
        $tituloPackingList->getFont()->setSize(14);
        $tituloPackingList->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("B2:E2");
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension($this->inicio)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $sheet->setCellValue('E'.$this->fin, '=SUM(E'. ($this->inicio + 1).':E'.($this->fin - 1) . ')');
        $sheet->getStyle("B".$this->fin . ':' . "E" . $this->fin)->getFont()->setBold(true);
    }
}
