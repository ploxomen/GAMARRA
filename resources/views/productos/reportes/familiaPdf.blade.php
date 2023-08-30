<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de familias</title>
</head>
<body>
    <style>
        @page{
            font-size: 13px !important;
        }
    </style>
    @include('ranking.export.headerExport')
    <h3 class="text-center">REPORTE DE FAMILIAS, SUBFAMILIAS Y PRODUCTOS</h3>
    <table border="1">
        <tbody>
            @php
                $estados = ["Descontinuado","Vigente"];
            @endphp
            @foreach ($familias as $key => $familia)
                <tr style="background: #ffafaf;">
                    <th>CODIGO FAMILIA</th>
                    <th>NOMBRE FAMILIA</th>
                </tr>
                <tr>
                    <td class="text-center">{{$familia->codigo}}</td>
                    <td class="text-center">{{$familia->nombre}}</td>
                </tr>
                @foreach ($familia->subFamila()->orderBY("codigo")->get() as $subfamilia)
                    <tr style="background: #afe2ff;">
                        <th>CODIGO SUBFAMILIA</th>
                        <th>NOMBRE SUBFAMILIA</th>
                    </tr>
                    <tr>
                        <td class="text-center">{{$subfamilia->codigo}}</td>
                        <td class="text-center">{{$subfamilia->nombre}}</td>
                    </tr>
                    @if ($subfamilia->productos->count())
                        <tr style="background: #eeffaf;">
                            <th>CODIGO PRODUCTO</th>
                            <th>NOMBRE PRODUCTO</th>
                        </tr>
                        @foreach ($subfamilia->productos()->orderBY("codigo")->get() as $producto)
                            <tr>
                                <td class="text-center">{{$producto->codigo}}</td>
                                <td class="text-center">{{$producto->nombreProducto}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>