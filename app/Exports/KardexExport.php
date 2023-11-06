<?php

namespace App\Exports;

use App\Models\KardexCliente;
use App\Models\KardexClienteCategoria;
use App\Models\KardexFardo;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KardexExport implements WithMultipleSheets
{
    protected $kardex;
    protected $filaInicial1 = 8;
    protected $filaInicial2 = 7;

    public function __construct(object $kardex)
    {
        $this->kardex = $kardex;
    }
    public function filtroPackingList48($filaInicial){
        $nroFilas = $filaInicial;
        $totalKilajes = 0;
        $totalCantidad = 0;
        $kardexLista = [];
        foreach ($this->kardex->fardos as $fardo){
            $fardos = [
                'numero' => $fardo->nro_fardo,
                'kilaje' => $fardo->kilaje,
                'cliente' => $fardo->clientes->nombreCliente,
            ];
            $totalKilajes += $fardo->kilaje;
            foreach ($fardo->productosDetalle as $detalle){
                $totalCantidad += $detalle->cantidad;
                $nroFilas++;
                $fardos['detalles'][] = [
                    'cantidad' => $detalle->cantidad,
                    'nombreProducto' => $detalle->productos->nombreProducto,
                    'presentacion' => $detalle->presentaciones->presentacion
                ];
            }
            $kardexLista[] = $fardos;
        }
        return ['kardex' => $kardexLista,'totalKilaje' => $totalKilajes,'totalCantidad' => $totalCantidad,'filaFinal' => $nroFilas];
    }
    public function filtroPackingList($filaInicial) {
        $nroFilas = $filaInicial;
        $clientes = $this->kardex->fardos()->select("id_cliente")->groupBy("id_cliente")->get();
        $kardexLista = [];
        foreach ($clientes as $cliente){
            $tasas = KardexCliente::where(['id_kardex' => $this->kardex->id,'id_cliente' => $cliente->id_cliente])->first();
            $totalProductos = 0;
            $usuarioModelo = $cliente->clientes->usuario;
            $clientesLista = [
                'telefono' => is_null($usuarioModelo) ? null : $usuarioModelo->telefono,
                'cliente' => $cliente->clientes->nombreCliente,
                'tasa_extranjera' => $this->kardex->tasa_extranjera
            ];
            foreach (KardexFardo::where(['id_kardex' => $this->kardex->id,'id_cliente' => $cliente->id_cliente])->get() as $fardo){
                $fardos = [
                    'numero' => $fardo->nro_fardo,
                    'kilaje' => $fardo->kilaje,
                ];
                foreach ($fardo->productosDetalle as $keyDetalle => $detalle) {
                    if($keyDetalle === 0){
                        $idCategoria = $detalle->productos->categoria->id;
                        $modeloCategoriaKardex = KardexClienteCategoria::where(['id_kardex_cliente' => $tasas->id,'id_categoria' => $idCategoria])->first();
                        $fardos['tasa'] = empty($modeloCategoriaKardex) ? 0 : $modeloCategoriaKardex->tasa;
                    }
                    $totalProductos++;
                    $nroFilas++;
                    $fardos['productos'][] = [
                        'cantidad' => $detalle->cantidad,
                        'nombreProducto' => $detalle->productos->nombreProducto,
                        'presentacion' => $detalle->presentaciones->presentacion
                    ];
                }
                $clientesLista['fardos'][] = $fardos; 
            }
            array_multisort(array_column($clientesLista['fardos'], 'tasa'), SORT_ASC, $clientesLista['fardos']);
            $clientesLista['totalProductos'] = $totalProductos;
            $kardexLista[] = $clientesLista;
        }
        return ['kardex' => $kardexLista,'filaFinal' => $nroFilas];
    }
    public function sheets(): array
    {
        $packingList = $this->filtroPackingList($this->filaInicial2);
        // dd($packingList);
        $packingList48 = $this->filtroPackingList48($this->filaInicial1);
        $sheets = [
            new PackingList48($packingList48['kardex'],$packingList48['totalCantidad'],$packingList48['totalKilaje'],$packingList48['filaFinal']),
            new PackingList($packingList['kardex'],$packingList['filaFinal'])
        ];
        return $sheets;
    }
}
