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
        $rango = "A" . $this->filaInicial . ":F" . $this->filaFinal;
        $tituloPackingList = $sheet->getStyle('A2');
        $tituloPackingList->getFont()->setBold(true);
        $tituloPackingList->getFont()->setUnderline(true);
        $tituloPackingList->getAlignment()->setHorizontal('center');
        $cabeceraTabla = $sheet->getStyle("A8:F8");
        $cabeceraTabla->getFont()->setBold(true);
        $sheet->getRowDimension(8)->setRowHeight(30);
        $sheet->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle($rango)->getAlignment()->setHorizontal('center');
        $sheet->getStyle($rango)->getAlignment()->setVertical('center');
        $sheet->getStyle("A".$this->filaFinal . ':' . "F" . $this->filaFinal)->getFont()->setBold(true);
    }
    public function title(): string
    {
        return 'PACKING LIST 48';
    }
}
