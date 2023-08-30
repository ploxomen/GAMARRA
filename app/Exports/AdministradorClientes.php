<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AdministradorClientes implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    private $clientes;
    private $lineaInicio = 4;
    private $lineaFin;

    public function __construct(Object $clientes,int $totalClientes){
        $this->clientes = $clientes;
        $this->lineaFin = $this->lineaInicio + $totalClientes;
    }
    public function view(): View
    {
        return view('ventas.reportes.clientesExcel', [
            'clientes' => $this->clientes,
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $rango = "B" . $this->lineaInicio . ":K" . $this->lineaFin;
        $titulo = $sheet->getStyle('B2');
        $titulo->getFont()->setBold(true);
        $titulo->getFont()->setUnderline(true);
        $titulo->getFont()->setSize(18);
        $titulo->getAlignment()->setHorizontal('center');
        $titulo->getAlignment()->setVertical('center');
        $cabeceraTabla = $sheet->getStyle("B4:K4");
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
        return 'REPORTE DE CLIENTES';
    }
}
