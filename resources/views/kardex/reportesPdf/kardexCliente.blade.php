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
        }
        table{
            border-collapse: collapse;
            width: 100%;
        }
        .titulo{
            text-align: center;
            text-decoration: underline;
        }
        .text-center{
            text-align: center;
        }
        .border{
            border: 1px solid black;
        }
    </style>
    <h2 class="titulo">KARDEX</h2>
    <h4 class="text-center">Cliente: {{$fardos[0]->clientes->nombreCliente}}</h4>
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
                $tasa = empty($kardexCliente) ? 0 : $kardexCliente->tasa;
            @endphp
            @foreach ($fardos as $fardo)
                @php
                    $rowspan = $fardo->productosDetalle()->count();
                    $peso = $peso + $fardo->kilaje;
                @endphp
                <tr>
                    <td class="text-center" rowspan="{{$rowspan}}">{{$fardo->nro_fardo}}</td>
                    @foreach ($fardo->productosDetalle as $key => $detalle)
                        @php
                            $cantidad = $cantidad + $detalle->cantidad;
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
    <div style="height: 50px;"></div>
    <table class="text-center">
        <tr>
            <td style="width: 50px;">
                <b> Tasa: </b> 
                <span>$ {{number_format($tasa,2)}}</span>
            </td>
            <td style="width: 150px;">
                <b>Peso total: </b>
                <span>{{$peso}}</span>
            </td>
            <td style="width: 100px;">
                <b>Total a pagar: </b>
                <span>$ {{number_format($peso * $tasa,2)}}</span> 
            </td>
        </tr>
    </table>
</body>
</html>