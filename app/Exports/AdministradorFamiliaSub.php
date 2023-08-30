<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdministradorFamiliaSub implements FromView,ShouldAutoSize,WithStyles,WithTitle
{
    private $familia;
    private $lineaInicio = 4;

    public function __construct(Object $familia,int $totalfamilia){
        $this->familia = $familia;
    }
    public function view(): View
    {
        return view('productos.reportes.familiaExcel', [
            'familias' => $this->familia,
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $titulo = $sheet->getStyle('B2');
        $titulo->getFont()->setBold(true);
        $titulo->getFont()->setUnderline(true);
        $titulo->getFont()->setSize(14);
        $titulo->getAlignment()->setHorizontal('center');
        $titulo->getAlignment()->setVertical('center');
        $conteoFamilia = 2;
        foreach ($this->familia as $familia) {
            $conteoFamilia += 2;
            $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFont()->setBold(true);
            $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB("ffafaf");
            foreach ($familia->subFamila as $subfamilia) {
                $nroProductos = $subfamilia->productos->count();
                $conteoFamilia += 2;
                $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFont()->setBold(true);
                $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB("afe2ff");
                if($nroProductos > 0){
                    $conteoFamilia += 2;
                    $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFont()->setBold(true);
                    $sheet->getStyle("B" . $conteoFamilia .":C" . $conteoFamilia)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB("eeffaf");
                }
                $conteoFamilia += $nroProductos === 0 ? 0 : $nroProductos - 1;
            }
        }
        $rango = "B" . $this->lineaInicio . ":C" . $conteoFamilia + 1;
        $sheet->getRowDimension(2)->setRowHeight(40);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
    }
    public function title(): string
    {
        return 'REPORTE DE FAMILIAS, SUBFAMILIAS Y PRODUCTOS';
    }
}
