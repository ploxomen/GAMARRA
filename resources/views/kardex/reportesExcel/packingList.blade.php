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
                    $inicioBusqueda = 0;
                    $comparacionTasa = 0;
                    $rowSpanGlobal = 0;
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
                            @php
                                $comparacionTasa++;
                            @endphp
                            @if ($keyProducto > 0)
                                <tr>
                            @endif
                            <td>{{$producto['cantidad']}}</td>
                            <td>{{$producto['nombreProducto']}}</td>
                            <td>{{$producto['presentacion']}}</td>
                            @if ($inicioBusqueda === 0 || (($rowSpanGlobal + 1) === $comparacionTasa))
                                @php
                                    if($inicioBusqueda > 0){
                                        $comparacionTasa = 1;
                                    }
                                    $rowspanAcumulado = 0;
                                    $tasaInicial = null;
                                    $kilajeAcumulado = 0;
                                    $recorrerClientesFardos = $cliente['fardos'];
                                @endphp
                                @for ($i = $inicioBusqueda; $i < count($recorrerClientesFardos); $i++)
                                    @php
                                        $tasaInicial = $recorrerClientesFardos[$i]['tasa'];
                                        $rowspanAcumulado = $rowspanAcumulado + count($recorrerClientesFardos[$i]['productos']);
                                        $kilajeAcumulado += $recorrerClientesFardos[$i]['kilaje'];
                                    @endphp
                                    @if(!isset($recorrerClientesFardos[$i + 1]) || ($recorrerClientesFardos[$i + 1]['tasa']!==$tasaInicial))
                                        <td rowspan="{{$rowspanAcumulado}}">{{$kilajeAcumulado}}</td>
                                        <td rowspan="{{$rowspanAcumulado}}">{{$tasaInicial}}</td>
                                        <td rowspan="{{$rowspanAcumulado}}"></td>
                                        <td rowspan="{{$rowspanAcumulado}}"></td>
                                        <td rowspan="{{$rowspanAcumulado}}"></td>
                                        @php
                                            $inicioBusqueda = $i + 1;
                                            $rowSpanGlobal = $rowspanAcumulado;
                                            break;
                                        @endphp
                                    @endif
                                @endfor
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