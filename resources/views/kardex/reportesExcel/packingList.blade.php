<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Packing List</title>
</head>
<body>
    <table>
        <tr>
            @for ($i = 0; $i < 8; $i++)
                <th></th>
            @endfor
            <th>
                <img src="{{public_path("img/logo-sin-fondo.png")}}" alt="logo de la empresa" width="120px">
            </th>
        </tr>
        <tr>
            <th colspan="11">
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
                <th style="background: navy; color:white;">TELEF.</th>
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
            @foreach ($kardex as $cliente)
                @php
                    $rowspanPrincipal = $cliente['totalProductos'];
                @endphp
                <tr>
                    <td rowspan="{{$rowspanPrincipal}}">{{$cliente['cliente']}}</td>
                    <td rowspan="{{$rowspanPrincipal}}">{{$cliente['telefono']}}</td>
                    @foreach ($cliente['fardos'] as $key => $fardo)
                        @php
                            $rowspanSecundario = count($fardo['productos']);
                        @endphp
                        @if ($key > 0)
                            <tr>
                        @endif
                        <td rowspan="{{$rowspanSecundario}}">{{$fardo['numero']}}</td>
                        @foreach ($fardo['productos'] as $keyProducto => $producto)
                            @if ($keyProducto > 0)
                                <tr>
                            @endif
                            <td>{{$producto['cantidad']}}</td>
                            <td>{{$producto['nombreProducto']}}</td>
                            <td>{{$producto['presentacion']}}</td>
                            @if ($key === 0 && $keyProducto === 0)
                                <td rowspan="{{$rowspanPrincipal}}">{{$cliente['totalKilaje']}}</td>
                                <td rowspan="{{$rowspanPrincipal}}">{{$cliente['tasa']}}</td>
                                <td rowspan="{{$rowspanPrincipal}}"></td>
                                <td rowspan="{{$rowspanPrincipal}}"></td>
                                <td rowspan="{{$rowspanPrincipal}}"></td>
                            @endif
                            </tr>
                        @endforeach
                    @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th>CANTIDAD</th>
                <th></th>
                <th></th>
                <th>T. KILOS</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>