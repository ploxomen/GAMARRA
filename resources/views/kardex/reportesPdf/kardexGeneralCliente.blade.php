<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kardex cliente</title>
</head>
<body>
    <style>
        @page{
            font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            margin: 50px 50px 20px 50px;
        }
        table{
            font-size: 13px;
            border-collapse: collapse;
            width: 100%;
        }
        .titulo{
            font-size: 18px;
            text-align: center;
            text-decoration: underline;
        }
        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        .border{
            border: 1px solid black;
        }
    </style>
    <table>
        <tr>
            <td class="text-right" rowspan="3" style="width: 100%; height: 50px;">
                <img src="{{public_path("img/logo-sin-fondo.png")}}" alt="logo de la empresa" width="100px">
            </td>
        </tr>
    </table>
    <h2 class="titulo">KARDEX GENERAL DEL CLIENTE {{isset($kardexs[0]->nombreCliente) ? $kardexs[0]->nombreCliente : ''}}</h2>
    @foreach ($kardexs as $kardex)
    <div style="page-break-inside:avoid;">
        @php
            $fardos = $kardex->kardex->fardos()->where('id_cliente',$kardex->idCliente)->get();
        @endphp
        <table style="margin-bottom: 25px;">
            <tr>
                <td>
                    <b>Kardex:</b>
                    <span>{{$kardex->nro_kardex}}</span>
                </td>
                <td>
                    <b>Fecha:</b> 
                    <span>{{$kardex->fecha_kardex}}</span>
                </td>
                {{-- <td>
                    <b>Cliente:</b> 
                    <span>{{$kardex->nombreCliente}}</span>
                </td> --}}
                <td>
                    <b>Guía aérea:</b>
                    <span>{{$kardex->guia_aerea}}</span>
                </td>
                
            </tr>
        </table>
        <table border="1">
            <thead>
                <tr>
                    <th>N° F.</th>
                    <th>CANT</th>
                    <th>DESCRIPCIÓN</th>
                    <th>PESO</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cantidad = 0;
                    $peso = 0;
                @endphp
                @foreach ($fardos as $keyFardo => $fardo)
                    @php
                        $rowspan = $fardo->productosDetalle()->count();
                        $peso = $peso + $fardo->kilaje;
                    @endphp
                    <tr>
                        <td class="text-center" rowspan="{{$rowspan}}">{{$fardo->nro_fardo}}</td>
                        @foreach ($fardo->productosDetalle as $key => $detalle)
                            @php
                                $cantidad = $cantidad + $detalle->cantidad;
                                if($key === 0){
                                    $categoriaProducto = $detalle->productos->id_categoria;
                                    $fardos[$keyFardo]['categoria'] = $categoriaProducto;
                                }
                            @endphp    
                            @if ($key > 0)
                                <tr>
                            @endif
                            <td class="text-center">{{$detalle->cantidad}}</td>
                            <td>{{$detalle->productos->nombreProducto}}</td>
                            @if ($key === 0)
                                <td class="text-center" rowspan="{{$rowspan}}">{{$fardo->kilaje}}</td>
                            @endif
                            </tr>
                        @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>{{$cantidad}}</th>
                    <th class="text-center">TOTAL DE PESO</th>
                    <th>{{$peso}}</th>
                </tr>
            </tfoot>
        </table>
        <div style="height: 10px;"></div>
        @php
            $totalPagar = 0;
        @endphp
        <table class="text-center" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Tasa</th>
                    <th>Peso total</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kardex->tasasCategorias as $categoria)
                    @php
                        $pesoCategorias = array_filter($fardos->toArray(),function($fardo)use($categoria){
                            return $fardo['categoria'] === $categoria->id_categoria;
                        });
                        $pesoTotal = array_sum(array_column($pesoCategorias,'kilaje'));
                        $pagoImporte = $pesoTotal * $categoria->tasa;
                        $totalPagar += $pagoImporte;
                    @endphp
                    <tr>
                        <td>{{$categoria->categoria->nombreCategoria}}</td>
                        <td>$ {{number_format($categoria->tasa,2)}}</td>
                        <td>{{$pesoTotal}}</td>
                        <td>$ {{number_format($pagoImporte,2)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td class="text-center" style="border-top: 1px solid black;">$ {{number_format($totalPagar,2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach
</body>
</html>