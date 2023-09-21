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
            margin: 10px 50px 20px 50px;
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
    <h2 class="titulo">KARDEX</h2>
    <table style="margin-bottom: 25px;">
        <tr>
            <td style="width: 500px;">
                <b>Cliente:</b> 
                <span>{{$fardos[0]->clientes->nombreCliente}}</span>
            </td>
            <td>
                <b>Fecha:</b> 
                <span>{{date('d/m/Y',strtotime($kardex->fechaCreada))}}</span>
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