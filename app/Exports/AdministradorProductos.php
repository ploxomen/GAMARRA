<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AdministradorProductos implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    private $productos;
    private $lineaInicio = 4;
    private $lineaFin;

    public function __construct(Object $productos,int $totalproductos){
        $this->productos = $productos;
        $this->lineaFin = $this->lineaInicio + $totalproductos;
    }
    public function view(): View
    {
        return view('productos.reportes.productosExcel', [
            'productos' => $this->productos,
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $rango = "B" . $this->lineaInicio . ":H" . $this->lineaFin;
        $titulo = $sheet->getStyle('B2');
        $titulo->getFont()->setBold(true);
        $titulo->getFont()->setUnderline(true);
        $titulo->getFont()->setSize(18);
        $titulo->getAlignment()->setHorizontal('center');
        $titulo->getAlignment()->setVertical('center');
        $cabeceraTabla = $sheet->getStyle("B4:H4");
        $cabeceraTabla->getAlignment()->setHorizontal('center');
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension(2)->setRowHeight(40);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        // $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
    }
    public function title(): string
    {
        return 'REPORTE DE PRODUCTOS';
    }
}
