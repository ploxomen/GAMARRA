<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ranking de proveedores</title>
</head>
<body>
    @include('ranking.export.headerExport')
    <h3 class="text-center">RANKING PROVEEDORES DESDE {{$fechaInicio . ' HASTA '. $fechaFin}}</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>PROVEEDORES</th>
                <th>PRODUCTOS</th>
                <th>CANTIDADES</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($datos as $k => $dato)
                @php
                    $total += $dato->cantidades;
                @endphp
                <tr>
                    <td>{{$k + 1}}</td>
                    <td>{{$dato->nombre_proveedor}}</td>
                    <td>{{$dato->nombreProducto}}</td>
                    <td>{{$dato->cantidades}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                <th>{{$total}}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>