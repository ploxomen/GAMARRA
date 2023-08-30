<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de productos</title>
</head>
<body>
    <style>
        @page{
            font-size: 13px !important;
        }
    </style>
    @include('ranking.export.headerExport')
    <h3 class="text-center">REPORTE DE PRODUCTOS</h3>
    <table border="1">
        <thead>
            <tr>
                <th>N°</th>
                <th>CODIGO</th>
                <th>PRODUCTO</th>
                <th>FAMILIA</th>
                <th>SUBFAMILIA</th>
                <th>PRECIO</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $estados = ["Descontinuado","Vigente"];
            @endphp
            @foreach ($productos as $key => $producto)
                @php
                    $estado = !isset($estados[$producto->productoEstado]) ? 'Estado no establecido' : $estados[$producto->productoEstado];
                @endphp
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$producto->productoCodigo}}</td>
                    <td>{{$producto->productoNombre}}</td>
                    <td>{{$producto->familiaNombre}}</td>
                    <td>{{$producto->familiaSubNombre}}</td>
                    <td>${{number_format($producto->precioVenta,2)}}</td>
                    <td>{{$estado === false ? 'Estado no establecido' : $estado}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>