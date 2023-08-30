<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AdministradorProveedor implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    private $proveedor;
    private $lineaInicio = 4;
    private $lineaFin;

    public function __construct(Object $proveedor,int $totalproveedor){
        $this->proveedor = $proveedor;
        $this->lineaFin = $this->lineaInicio + $totalproveedor;
    }
    public function view(): View
    {
        return view('compras.reportes.proveedorExcel', [
            'proveedores' => $this->proveedor,
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $rango = "B" . $this->lineaInicio . ":I" . $this->lineaFin;
        $titulo = $sheet->getStyle('B2');
        $titulo->getFont()->setBold(true);
        $titulo->getFont()->setUnderline(true);
        $titulo->getFont()->setSize(18);
        $titulo->getAlignment()->setHorizontal('center');
        $titulo->getAlignment()->setVertical('center');
        $cabeceraTabla = $sheet->getStyle("B4:I4");
        $cabeceraTabla->getFont()->setBold(true);
        $cabeceraTabla->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(2)->setRowHeight(40);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        // $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
    }
    public function title(): string
    {
        return 'REPORTE DE PROVEEDORES';
    }
}
