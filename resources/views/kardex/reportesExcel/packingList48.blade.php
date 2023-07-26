<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Packing List 48</title>
</head>
<body>
    <table>
        <tr></tr>
        <tr>
            <th colspan="6">
                <span>PACKING LIST</span>
            </th>
        </tr>
        <tr></tr>
        <tr>
            <td>DATE</td>
            <td>{{date('d/m/Y')}}</td>
        </tr>
        <tr>
            <td>TOTAL BULTOS</td>
            <td></td>
        </tr>
        <tr>
            <td>INVOICE</td>
            <td></td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th style="background: navy; color:white;">N° FARDO</th>
                <th style="background: navy; color:white;">CANTIDAD</th>
                <th style="background: navy; color:white;">DESCRIPCION</th>
                <th style="background: navy; color:white;">UNIDAD</th>
                <th style="background: navy; color:white;">KILAJES</th>
                <th style="background: navy; color:white;">CLIENTES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kardex as $fardo)
                @php
                    $rowspan = count($fardo['detalles']);
                @endphp
                <tr>
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
                            <td rowspan="{{$rowspan}}">{{$fardo['cliente']}}</td>
                        @endif
                        </tr>
                    @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL PRENDAS</th>
                <th>{{$cantidades}}</th>
                <th></th>
                <th>TOTAL KILOS</th>
                <th>{{$kilajes}}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>