<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Packing List</title>
</head>
<body>
    <table>
        <tr></tr>
        <tr>
            <th colspan="10">
                <span>PACKING LIST DE CLIENTES</span>
            </th>
        </tr>
        <tr></tr>
        <tr>
            <td>GUIA AEREA</td>
            <td></td>
        </tr>
        <tr>
            <td>FECHA</td>
            <td>{{date('d/m/Y')}}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th style="background: navy; color:white;">CLIENTE</th>
                <th style="background: navy; color:white;">N° FARDO</th>
                <th style="background: navy; color:white;">CANTIDAD</th>
                <th style="background: navy; color:white;">DESCRIPCION</th>
                <th style="background: navy; color:white;">UNIDAD</th>
                <th style="background: navy; color:white;">KILAJES</th>
                <th style="background: navy; color:white;">TASA</th>
                <th style="background: navy; color:white;">X COBRAR</th>
                <th style="background: navy; color:white;">X PAGAR</th>
                <th style="background: navy; color:white;">X PAGAR F.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kardex as $fardo)
                @php
                    $rowspan = count($fardo['detalles']);
                @endphp
                <tr>
                    <td rowspan="{{$rowspan}}">{{$fardo['cliente']}}</td>
                    <td rowspan="{{$rowspan}}">{{$fardo['numero']}}</td>
                    @foreach ($fardo['detalles'] as $key => $detalle)
                        @if ($key > 0)
                            <tr>
                        @endif
                        <td>{{$detalle['cantidad']}}</td>
                        <td>{{$detalle['nombreProducto']}}</td>
                        <td>{{$detalle['presentacion']}}</td>
                        @if ($key === 0)
                            <td rowspan="{{$rowspan}}">{{$fardo['kilaje']}}</td>
                            <td rowspan="{{$rowspan}}">{{$fardo['tasa']}}</td>
                            <td rowspan="{{$rowspan}}"></td>
                            <td rowspan="{{$rowspan}}"></td>
                            <td rowspan="{{$rowspan}}"></td>
                        @endif
                        </tr>
                    @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>CANTIDAD</th>
                <th>{{$cantidades}}</th>
                <th></th>
                <th>T. KILOS</th>
                <th>{{$kilajes}}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>